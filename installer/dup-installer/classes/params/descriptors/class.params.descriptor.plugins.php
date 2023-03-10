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
final class DUPX_Paramas_Descriptor_plugins implements DUPX_Interface_Paramas_Descriptor
{

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $params[DUPX_Paramas_Manager::PARAM_PLUGINS] = new DUPX_Param_item_form_plugins(
            DUPX_Paramas_Manager::PARAM_PLUGINS,
            DUPX_Param_item_form_plugins::TYPE_ARRAY_STRING,
            DUPX_Param_item_form_plugins::FORM_TYPE_PLUGINS_SELECT,
            array(
            'default'          => array(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            ),
            array(
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            })
        );

        $params[DUPX_Paramas_Manager::PARAM_IGNORE_PLUGINS] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_IGNORE_PLUGINS,
            DUPX_Param_item::TYPE_ARRAY_STRING,
            array(
            'default'          => array(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_FORCE_DIABLE_PLUGINS] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_FORCE_DIABLE_PLUGINS,
            DUPX_Param_item::TYPE_ARRAY_STRING,
            array(
            'default'          => array(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
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