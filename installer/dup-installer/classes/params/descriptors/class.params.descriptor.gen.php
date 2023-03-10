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
final class DUPX_Paramas_Descriptor_generic implements DUPX_Interface_Paramas_Descriptor
{

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();

        $params[DUPX_Paramas_Manager::PARAM_SECURE_PASS] = new DUPX_Param_item_form_pass(
            DUPX_Paramas_Manager::PARAM_SECURE_PASS,
            DUPX_Param_item_form_pass::TYPE_STRING,
            DUPX_Param_item_form_pass::FORM_TYPE_PWD_TOGGLE,
            array(
            'persistence'      => false,
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label' => 'Enter Password'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_SECURE_OK] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_SECURE_OK,
            DUPX_Param_item_form::TYPE_BOOL,
            array(
            'default' => false
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_FILE_PERMS_VALUE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_FILE_PERMS_VALUE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(
            'default'          => '644',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => '/^[ugorwx,\s\+\-0-7]+$/' // octal + ugo rwx,
            ),
            array(
            'status'         => DupProSnapLibOSU::isWindows() ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED,
            'wrapperClasses' => array('display-inline-block')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_SET_FILE_PERMS] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SET_FILE_PERMS,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_SWITCH,
            array(
            'default' => !DupProSnapLibOSU::isWindows()
            ),
            array(
            'status'         => DupProSnapLibOSU::isWindows() ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED,
            'label'          => 'File permissions:',
            'checkboxLabel'  => 'All files',
            'wrapperClasses' => array('display-inline-block'),
            'attr'           => array(
                'onclick' => "jQuery('#".$params[DUPX_Paramas_Manager::PARAM_FILE_PERMS_VALUE]->getFormItemId()."').prop('disabled', !jQuery(this).is(':checked'));"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DIR_PERMS_VALUE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DIR_PERMS_VALUE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(
            'default'          => '755',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => '/^[ugorwx,\s\+\-0-7]+$/' // octal + ugo rwx
            ),
            array(
            'status'         => DupProSnapLibOSU::isWindows() ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED,
            'wrapperClasses' => array('display-inline-block')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_SET_DIR_PERMS] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SET_DIR_PERMS,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_SWITCH,
            array(
            'default' => !DupProSnapLibOSU::isWindows()
            ),
            array(
            'status'         => DupProSnapLibOSU::isWindows() ? DUPX_Param_item_form::STATUS_SKIP : DUPX_Param_item_form::STATUS_ENABLED,
            'label'          => 'Dir permissions:',
            'checkboxLabel'  => 'All Directories',
            'wrapperClasses' => array('display-inline-block'),
            'attr'           => array(
                'onclick' => "jQuery('#".$params[DUPX_Paramas_Manager::PARAM_DIR_PERMS_VALUE]->getFormItemId()."').prop('disabled', !jQuery(this).is(':checked'));"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_SAFE_MODE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SAFE_MODE,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'      => 0,
            'acceptValues' => array(0, 1, 2)),
            array(
            'label'  => 'Safe Mode:',
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'options' => array(
                new DUPX_Param_item_form_option(0, 'Light'),
                new DUPX_Param_item_form_option(1, 'Basic'),
                new DUPX_Param_item_form_option(2, 'Advanced')
            ),
            'attr'    => array(
                'onchange' => 'DUPX.onSafeModeSwitch();'
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_FILE_TIME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_FILE_TIME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => 'current',
            'acceptValues' => array(
                'current',
                'original'
            )),
            array(
            'label'   => 'File Times:',
            'options' => array(
                new DUPX_Param_item_form_option('current', 'Current', DUPX_Param_item_form_option::OPT_ENABLED, array('title' => 'Set the files current date time to now')),
                new DUPX_Param_item_form_option('original', 'Original', DUPX_Param_item_form_option::OPT_ENABLED, array('title' => 'Keep the files date time the same'))
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_LOGGING] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_LOGGING,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => DUPX_Log::LV_DEFAULT,
            'acceptValues' => array(
                DUPX_Log::LV_DEFAULT,
                DUPX_Log::LV_DETAILED,
                DUPX_Log::LV_DEBUG,
                DUPX_Log::LV_HARD_DEBUG,
            )),
            array(
            'label'   => 'Logging:',
            'options' => array(
                new DUPX_Param_item_form_option(DUPX_Log::LV_DEFAULT, 'Light'),
                new DUPX_Param_item_form_option(DUPX_Log::LV_DETAILED, 'Detailed'),
                new DUPX_Param_item_form_option(DUPX_Log::LV_DEBUG, 'Debug'),
                // enabled only with overwrite params
                new DUPX_Param_item_form_option(DUPX_Log::LV_HARD_DEBUG, 'Hard debug', DUPX_Param_item_form_option::OPT_HIDDEN)
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Remove Inactive Plugins and Themes:',
            'checkboxLabel' => 'Remove inactive themes and plugins.',
            'status'        => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'wrapperId' => 'remove-redundant-row',
            'subNote'   => DUPX_Conf_Utils::showMultisite() ? 'When checked during a subsite to standalone migration, inactive users will also be removed.' : null
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_RECOVERY_LINK] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_RECOVERY_LINK,
            DUPX_Param_item_form_pass::TYPE_STRING,
            array(
            'default' => ''
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_FROM_SITE_IMPORT_INFO] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_FROM_SITE_IMPORT_INFO,
            DUPX_Param_item_form_pass::TYPE_ARRAY_MIXED,
            array(
            'default' => array()
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