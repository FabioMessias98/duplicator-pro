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
final class DUPX_Paramas_Descriptor_validation implements DUPX_Interface_Paramas_Descriptor
{

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $params[DUPX_Paramas_Manager::PARAM_VALIDATION_LEVEL] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_VALIDATION_LEVEL,
            DUPX_Param_item::TYPE_INT,
            array(
            'default'      => DUPX_Validation_abstract_item::LV_FAIL,
            'acceptValues' => array(
                DUPX_Validation_abstract_item::LV_FAIL,
                DUPX_Validation_abstract_item::LV_HARD_WARNING,
                DUPX_Validation_abstract_item::LV_SOFT_WARNING,
                DUPX_Validation_abstract_item::LV_GOOD,
                DUPX_Validation_abstract_item::LV_PASS
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_VALIDATION_ACTION_ON_START] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_VALIDATION_ACTION_ON_START,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default'      => DUPX_Validation_manager::ACTION_ON_START_NORMAL,
            'acceptValues' => array(
                DUPX_Validation_manager::ACTION_ON_START_NORMAL,
                DUPX_Validation_manager::ACTION_ON_START_AUTO,
                DUPX_Validation_manager::ACTION_ON_START_SKIP
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_VALIDATION_SHOW_ALL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_VALIDATION_SHOW_ALL,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_SWITCH,
            array(
            'default' => false
            ),
            array(
            'label'          => 'Show all',
            'wrapperClasses' => 'align-right'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_ACCEPT_TERM_COND] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_ACCEPT_TERM_COND,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => '',
            'checkboxLabel' => 'I have read and accept all <a href="#" onclick="DUPX.viewTerms()" >terms &amp; notices</a>*',
            'subNote'       => '* required to continue',
            'attr'          => array(
                'onclick' => 'DUPX.acceptWarning();'
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