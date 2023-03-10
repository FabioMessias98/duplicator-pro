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
final class DUPX_Paramas_Descriptor_replace implements DUPX_Interface_Paramas_Descriptor
{

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $params[DUPX_Paramas_Manager::PARAM_BLOGNAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_BLOGNAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => function ($value) {
                $value = DupProSnapLibUtil::sanitize_non_stamp_chars_and_newline($value);
                return htmlspecialchars_decode((empty($value) ? 'No Blog Title Set' : $value), ENT_QUOTES);
            }
            ), array(
            'label'  => 'Site Title:',
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            }
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CUSTOM_SEARCH] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_CUSTOM_SEARCH,
            DUPX_Param_item_form_urlmapping::TYPE_ARRAY_STRING,
            array(
            'default'          => array(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CUSTOM_REPLACE] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_CUSTOM_REPLACE,
            DUPX_Param_item_form_urlmapping::TYPE_ARRAY_STRING,
            array(
            'default'          => array(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_EMPTY_SCHEDULE_STORAGE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_EMPTY_SCHEDULE_STORAGE,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => $params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE]->getValue() ? false : true
            ),
            array(
            'label'         => 'Cleanup:',
            'checkboxLabel' => 'Remove schedules and storage endpoints'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_EMAIL_REPLACE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_EMAIL_REPLACE,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Email Domains:',
            'checkboxLabel' => 'Update'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_FULL_SEARCH] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_FULL_SEARCH,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Database Search:',
            'checkboxLabel' => 'Full Search Mode'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_POSTGUID] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_POSTGUID,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Post GUID:',
            'checkboxLabel' => 'Keep Unchanged'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_MAX_SERIALIZE_CHECK] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_MAX_SERIALIZE_CHECK,
            DUPX_Param_item_form::TYPE_INT,
            DUPX_Param_item_form::FORM_TYPE_NUMBER,
            array(
            'default' => DUPX_Constants::DEFAULT_MAX_STRLEN_SERIALIZED_CHECK_IN_M
            ),
            array(
            'min'              => 0,
            'max'              => 99,
            'step'             => 1,
            'wrapperClasses'   => array('small'),
            'label'            => 'Max size check for serialize objects:',
            'postfixElement'   => 'label',
            'postfixElemLabel' => 'MB'
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