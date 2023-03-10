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
final class DUPX_Paramas_Descriptor_configs implements DUPX_Interface_Paramas_Descriptor
{

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        if (!isset($params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE])) {
            throw new Exception('Init engine descriptor before');
        }

        $params[DUPX_Paramas_Manager::PARAM_WP_CONFIG] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_WP_CONFIG,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => 'modify',
            'acceptValues' => array(
                'modify',
                'nothing',
                'new'
            )),
            array(
            'label'  => 'WordPress wp-config:',
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'options' => array(
                new DUPX_Param_item_form_option('modify', 'Modify original'),
                new DUPX_Param_item_form_option('nothing', 'Do nothing'),
                new DUPX_Param_item_form_option('new', 'Create new from wp-config sample')
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => $params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE]->getValue() ? 'original' : 'new',
            'acceptValues' => array(
                'new',
                'original',
                'nothing'
            )),
            array(
            'label'  => 'Apache .htaccess:',
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'options' => array(
                new DUPX_Param_item_form_option('new', 'Create new'),
                new DUPX_Param_item_form_option('original', 'Retain original'),
                new DUPX_Param_item_form_option('nothing', 'Do nothing')
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_OTHER_CONFIG] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_OTHER_CONFIG,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => $params[DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE]->getValue() ? 'original' : 'new',
            'acceptValues' => array(
                'new',
                'original',
                'nothing'
            )),
            array(
            'label'  => 'Other Configurations (web.config, user.ini):',
            'status' => function($paramObj) {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'options' => array(
                new DUPX_Param_item_form_option('new', 'Reset'),
                new DUPX_Param_item_form_option('original', 'Retain original'),
                new DUPX_Param_item_form_option('nothing', 'Do nothing')
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