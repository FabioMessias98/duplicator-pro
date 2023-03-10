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
final class DUPX_Paramas_Descriptor_controller implements DUPX_Interface_Paramas_Descriptor
{

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $params[DUPX_Paramas_Manager::PARAM_FINAL_REPORT_DATA] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_FINAL_REPORT_DATA,
            DUPX_Param_item::TYPE_ARRAY_MIXED,
            array(
            'default' => array(
                'extraction' => array(
                    'table_count' => 0,
                    'table_rows'  => 0,
                    'query_errs'  => 0,
                ),
                'replace'    => array(
                    'scan_tables' => 0,
                    'scan_rows'   => 0,
                    'scan_cells'  => 0,
                    'updt_tables' => 0,
                    'updt_rows'   => 0,
                    'updt_cells'  => 0,
                    'errsql'      => 0,
                    'errser'      => 0,
                    'errkey'      => 0,
                    'errsql_sum'  => 0,
                    'errser_sum'  => 0,
                    'errkey_sum'  => 0,
                    'err_all'     => 0,
                    'warn_all'    => 0,
                    'warnlist'    => array()
                )
            )
            )
        );




        $params[DUPX_Paramas_Manager::PARAM_INSTALLER_MODE] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_INSTALLER_MODE,
            DUPX_Param_item::TYPE_INT,
            array(
            'default'      => DUPX_InstallerState::MODE_UNKNOWN,
            'acceptValues' => array(
                DUPX_InstallerState::MODE_UNKNOWN,
                DUPX_InstallerState::MODE_STD_INSTALL,
                DUPX_InstallerState::MODE_OVR_INSTALL,
                DUPX_InstallerState::MODE_BK_RESTORE
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA,
            DUPX_Param_item::TYPE_ARRAY_MIXED,
            array(
            'default' => array('db')
            )
        );


        $params[DUPX_Paramas_Manager::PARAM_DEBUG] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_DEBUG,
            DUPX_Param_item::TYPE_BOOL,
            array(
            'persistence' => true,
            'default'     => false
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DEBUG_PARAMS] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_DEBUG_PARAMS,
            DUPX_Param_item::TYPE_BOOL,
            array(
            'persistence' => true,
            'default'     => false
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CTRL_ACTION] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CTRL_ACTION,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_HIDDEN,
            array(
            'persistence'  => false,
            'default'      => '',
            'acceptValues' => array(
                '',
                'ajax',
                'secure',
                'ctrl-step1',
                'ctrl-step2',
                'ctrl-step3',
                'ctrl-step4',
                'help'
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_STEP_ACTION] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_STEP_ACTION,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'persistence'  => false,
            'default'      => '',
            'acceptValues' => array(
                '',
                DUPX_CTRL::ACTION_STEP_INIZIALIZED,
                DUPX_CTRL::ACTION_STEP_ON_VALIDATE,
                DUPX_CTRL::ACTION_STEP_SET_TEMPLATE
            ))
        );

        $params[DUPX_Security::CTRL_TOKEN] = new DUPX_Param_item_form(
            DUPX_Security::CTRL_TOKEN,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_HIDDEN,
            array(
            'persistence'      => false,
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_ROUTER_ACTION] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_ROUTER_ACTION,
            DUPX_Param_item::TYPE_STRING,
            array(
            'persistence'  => false,
            'default'      => 'router',
            'acceptValues' => array(
                'router'
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_TEMPLATE] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_TEMPLATE,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default'      => DUPX_Template::TEMPLATE_BASE,
            'acceptValues' => array(
                DUPX_Template::TEMPLATE_BASE,
                DUPX_Template::TEMPLATE_ADVANCED,
                DUPX_Template::TEMPLATE_IMPORT_BASE,
                DUPX_Template::TEMPLATE_RECOVERY
            ))
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