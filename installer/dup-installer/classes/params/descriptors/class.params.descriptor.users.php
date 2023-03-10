<?php
/**
 * Installer params manager
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @final class DUPX_Paramas_Descriptor_urls_paths
  {
  package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class DUPX_Paramas_Descriptor_users implements DUPX_Interface_Paramas_Descriptor
{

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {

        $params[DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'          => 0,
            'sanitizeCallback' => function ($value) {
                // disable keep users for some db actions
                switch (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_ACTION)) {
                    case DUPX_DBInstall::DBACTION_CREATE:
                    case DUPX_DBInstall::DBACTION_MANUAL:
                    case DUPX_DBInstall::DBACTION_ONLY_CONNECT:
                        return 0;
                    case DUPX_DBInstall::DBACTION_EMPTY:
                    case DUPX_DBInstall::DBACTION_RENAME:
                        return (int) $value;
                }
            },
            'validateCallback' => function ($value) {
                if ($value == 0) {
                    return true;
                }
                $overwriteData = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
                foreach ($overwriteData['adminUsers'] as $user) {
                    if ($value == $user['id']) {
                        return true;
                    }
                }
                return false;
            }
            ),
            array(
            'status' => function () {
                if (DUPX_MU::newSiteIsMultisite()) {
                    return DUPX_Param_item_form::STATUS_SKIP;
                }

                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                }

                $overwriteData = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
                if (!empty($overwriteData['adminUsers'])) {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                }
            },
            'label'   => 'Keep users:',
            'options' => function ($item) {
                $result        = array(
                    new DUPX_Param_item_form_option(0, ' - DISABLED - '),
                );
                $overwriteData = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
                if (!empty($overwriteData['adminUsers'])) {
                    foreach ($overwriteData['adminUsers'] as $userData) {
                        $result[] = new DUPX_Param_item_form_option($userData['id'], $userData['user_login']);
                    }
                }
                return $result;
            },
            'subNote' => 'Keep users of the current site and eliminates users of the original site.<br><b>Assign all pages and posts to the selected user.</b>'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_USERS_PWD_RESET] = new DUPX_Param_item_form_users_pass_reset(
            DUPX_Paramas_Manager::PARAM_USERS_PWD_RESET,
            DUPX_Param_item_form_users_pass_reset::TYPE_ARRAY_STRING,
            DUPX_Param_item_form_users_pass_reset::FORM_TYPE_USERS_PWD_RESET,
            array(// ITEM ATTRIBUTES
            'default' => array_map(function ($value) {
                    return '';
                }, DUPX_ArchiveConfig::getInstance()->getUsersLists()),
            'sanitizeCallback'                                   => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateCallback'                                   => function ($value) {
                return strlen($value) == 0 || strlen($value) >= DUPX_Constants::MIN_NEW_PASSWORD_LEN;
            },
            'invalidMessage' => 'can\'t have less than '.DUPX_Constants::MIN_NEW_PASSWORD_LEN.' characters'
            ), array(// FORM ATTRIBUTES
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS) > 0) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'label'   => 'Existing user reset password:',
            'classes' => 'strength-pwd-check',
            'attr'    => array(
                'title'       => DUPX_Constants::MIN_NEW_PASSWORD_LEN.' characters minimum',
                'placeholder' => "Reset user password"
            )
            )
        );
    }

    /**
     * 
     * @param DUPX_Param_item[] $params
     */
    public static function updateParamsAfterOverwrite(&$params)
    {
        
    }
}