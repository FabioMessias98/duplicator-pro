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
final class DUPX_Paramas_Descriptor_new_admin implements DUPX_Interface_Paramas_Descriptor
{

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_SWITCH,
            array(
            'default' => false
            ),
            array(
            'label'  => 'Create New User:',
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS) > 0) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'checkboxLabel' => ''
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_NAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_NAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateCallback' => function ($value) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW)) {
                    return strlen($value) >= 4;
                } else {
                    $value = '';
                    return true;
                }
            }
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'Username:',
            'classes' => 'new-admin-field',
            'attr'    => array(
                'title'       => '4 characters minimum',
                'placeholder' => "(4 or more characters)"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_PASSWORD] = new DUPX_Param_item_form_pass(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_PASSWORD,
            DUPX_Param_item_form_pass::TYPE_STRING,
            DUPX_Param_item_form_pass::FORM_TYPE_PWD_TOGGLE,
            array(
            'default'          => $GLOBALS['DUPX_AC']->cpnl_pass,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateCallback' => function ($value) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW)) {
                    return strlen($value) >= DUPX_Constants::MIN_NEW_PASSWORD_LEN;
                } else {
                    $value = '';
                    return true;
                }
            },
            'invalidMessage' => 'can\'t have less than '.DUPX_Constants::MIN_NEW_PASSWORD_LEN.' characters'
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'Password:',
            'classes' => array('strength-pwd-check', 'new-admin-field'),
            'attr'    => array(
                'placeholder' => '('.DUPX_Constants::MIN_NEW_PASSWORD_LEN.' or more characters)',
                'title'       => DUPX_Constants::MIN_NEW_PASSWORD_LEN.' characters minimum'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_MAIL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_MAIL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateCallback' => function ($value) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW)) {
                    if (strlen($value) < 4 || strpos($value, '@') < 1) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    $value = '';
                    return true;
                }
            }
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'Email:',
            'classes' => 'new-admin-field',
            'attr'    => array(
                'title'       => '4 characters minimum',
                'placeholder' => "(4 or more characters)"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_NICKNAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_NICKNAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim')
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'Nickname:',
            'classes' => 'new-admin-field',
            'attr'    => array(
                'title'       => 'if username is empty',
                'placeholder' => "(if username is empty)"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_FIRST_NAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_FIRST_NAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim')
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'First Name:',
            'classes' => 'new-admin-field',
            'attr'    => array(
                'title'       => 'optional',
                'placeholder' => "(optional)"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_WP_ADMIN_LAST_NAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_ADMIN_LAST_NAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim')
            ),
            array(
            'status'  => array(__CLASS__, 'getStatuOfNewAdminParams'),
            'label'   => 'Last Name:',
            'classes' => 'new-admin-field',
            'attr'    => array(
                'title'       => 'optional',
                'placeholder' => "(optional)"
            )
            )
        );
    }

    /**
     *
     * @return string
     */
    public static function getStatuOfNewAdminParams()
    {
        if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW)) {
            return DUPX_Param_item_form::STATUS_ENABLED;
        } else {
            return DUPX_Param_item_form::STATUS_DISABLED;
        }
    }

    /**
     * 
     * @param DUPX_Param_item[] $params
     */
    public static function updateParamsAfterOverwrite(&$params)
    {
        
    }
}