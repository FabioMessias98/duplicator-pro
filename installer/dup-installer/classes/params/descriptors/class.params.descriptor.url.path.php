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
final class DUPX_Paramas_Descriptor_urls_paths implements DUPX_Interface_Paramas_Descriptor
{

    const INVALID_PATH_EMPTY = 'can\'t be empty';
    const INVALID_URL_EMPTY  = 'can\'t be empty';

    public static function init(&$params)
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();
        $paths          = $archive_config->getRealValue('archivePaths');

        $oldMainPath = $paths->home;
        $newMainPath = DUPX_ROOT;

        $oldHomeUrl = rtrim($archive_config->getRealValue('homeUrl'), '/');
        $newHomeUrl = rtrim(DUPX_ROOT_URL, '/');

        $oldSiteUrl      = rtrim($archive_config->getRealValue('siteUrl'), '/');
        $oldContentUrl   = rtrim($archive_config->getRealValue('contentUrl'), '/');
        $oldUploadUrl    = rtrim($archive_config->getRealValue('uploadBaseUrl'), '/');
        $oldPluginsUrl   = rtrim($archive_config->getRealValue('pluginsUrl'), '/');
        $oldMuPluginsUrl = rtrim($archive_config->getRealValue('mupluginsUrl'), '/');

        $oldWpAbsPath       = $paths->abs;
        $oldContentPath     = $paths->wpcontent;
        $oldUploadsBasePath = $paths->uploads;
        $oldPluginsPath     = $paths->plugins;
        $oldMuPluginsPath   = $paths->muplugins;

        $defValEdit = "This default value is automatically generated.\n"
            ."Change it only if you're sure you know what you're doing!";


        $params[DUPX_Paramas_Manager::PARAM_URL_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_URL_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default' => $oldHomeUrl
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $newHomeUrl,
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizeUrl'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'New Site URL:',
            'wrapperClasses'   => array('revalidate-on-change', 'cant-be-empty'),
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldHomeUrl).'</b>',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'get',
            'postfixBtnAction' => 'DUPX.getNewUrlByDomObj(this);'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_PATH_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default' => $oldMainPath
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => $newMainPath,
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizePath'),
            'validateCallback' => function ($value) {
                if (!is_dir($value)) {
                    return false;
                }

                // don't check the return of chmod, if fail the installer must continue
                DupProSnapLibIOU::chmod($value, 'u+rwx');
                return true;
            },
            'invalidMessage' => 'The new path must be an existing folder on the server.<br>'
            .'It is not possible to continue the installation without first creating the folder.'
            ), array(// FORM ATTRIBUTES
            'label'  => 'New Path:',
            'status' => function () {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_TEMPLATE) === DUPX_Template::TEMPLATE_ADVANCED) {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_INFO_ONLY;
                }
            },
            'subNote'        => 'Old value: <b>'.DUPX_U::esc_html($oldMainPath).'</b>',
            'wrapperClasses' => array('revalidate-on-change', 'cant-be-empty')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_SITE_URL_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_SITE_URL_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default' => $oldSiteUrl
            )
        );

        $wrapClasses    = array('revalidate-on-change', 'cant-be-empty', 'auto-updatable', 'autoupdate-enabled');
        $postFixElement = 'button';
        $status         = DUPX_Param_item_form::STATUS_READONLY;

        $params[DUPX_Paramas_Manager::PARAM_SITE_URL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_SITE_URL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => '', // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizeUrl'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => ' WP core URL:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldSiteUrl).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getFormItemId()
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_CONTENT_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_PATH_CONTENT_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default' => $oldContentPath
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => '', // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizePath'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'WP-content path:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldContentPath).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getFormItemId()
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default' => $oldWpAbsPath
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => '', // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizePath'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'WP core path:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldWpAbsPath).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getFormItemId()
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default' => $oldUploadsBasePath
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => '', // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizePath'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'Uploads path:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldUploadsBasePath).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getFormItemId()
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_CONTENT_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_URL_CONTENT_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default' => $oldContentUrl
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => '', // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizeUrl'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'WP-content URL:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldContentUrl).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getFormItemId()
            )
            )
        );


        $params[DUPX_Paramas_Manager::PARAM_URL_UPLOADS_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_URL_UPLOADS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(// ITEM ATTRIBUTES
            'default' => $oldUploadUrl
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => '', // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizeUrl'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'Uploads URL:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldUploadUrl).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getFormItemId()
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_PLUGINS_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_URL_PLUGINS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(// ITEM ATTRIBUTES
            'default' => $oldPluginsUrl
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => '', // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizeUrl'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'Plugins URL:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldPluginsUrl).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getFormItemId()
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default'          => $oldPluginsPath,
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizePath'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => '', // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizePath'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'Plugins path:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldPluginsPath).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getFormItemId()
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default' => $oldMuPluginsUrl
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => '', // if empty is generate automatically on ctrl params s0
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizeUrl'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validateUrlWithScheme'),
            'invalidMessage'   => self::INVALID_URL_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'MU-plugins URL:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldMuPluginsUrl).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getFormItemId()
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_OLD] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_OLD,
            DUPX_Param_item_form::TYPE_STRING,
            array(
            'default' => $oldMuPluginsPath
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(// ITEM ATTRIBUTES
            'default'          => '',
            'sanitizeCallback' => array('DUPX_Paramas_Descriptors', 'sanitizePath'),
            'validateCallback' => array('DUPX_Paramas_Descriptors', 'validatePath'),
            'invalidMessage'   => self::INVALID_PATH_EMPTY
            ), array(// FORM ATTRIBUTES
            'label'            => 'MU-plugins path:',
            'status'           => $status,
            'postfixElement'   => $postFixElement,
            'postfixElemLabel' => 'Auto',
            'postfixBtnAction' => 'DUPX.autoUpdateToggle(this, '.DupProSnapJsonU::wp_json_encode($defValEdit).');',
            'subNote'          => 'Old value: <b>'.DUPX_U::esc_html($oldMuPluginsPath).'</b>',
            'wrapperClasses'   => $wrapClasses,
            'wrapperAttr'      => array(
                'data-auto-update-from-input' => $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getFormItemId()
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
        DUPX_Paramas_Manager::getInstance();

        $archive_config = DUPX_ArchiveConfig::getInstance();
        $paths          = $archive_config->getRealValue('archivePaths');

        $oldMainPath = $paths->home;
        $newMainPath = $params[DUPX_Paramas_Manager::PARAM_PATH_NEW]->getValue();

        $oldHomeUrl = rtrim($archive_config->getRealValue('homeUrl'), '/');
        $newHomeUrl = $params[DUPX_Paramas_Manager::PARAM_URL_NEW]->getValue();

        $oldSiteUrl      = rtrim($archive_config->getRealValue('siteUrl'), '/');
        $oldContentUrl   = rtrim($archive_config->getRealValue('contentUrl'), '/');
        $oldUploadUrl    = rtrim($archive_config->getRealValue('uploadBaseUrl'), '/');
        $oldPluginsUrl   = rtrim($archive_config->getRealValue('pluginsUrl'), '/');
        $oldMuPluginsUrl = rtrim($archive_config->getRealValue('mupluginsUrl'), '/');

        // if empty value isn't overwritten
        if (strlen($params[DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW]->getValue()) == 0) {
            $newVal = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $paths->abs); // if empty is generate automatically on ctrl params s0
            $params[DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW]->setValue($newVal);
        }

        // if empty value isn't overwritten
        if (strlen($params[DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW]->getValue()) == 0) {
            $newVal = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $paths->wpcontent); // if empty is generate automatically on ctrl params s0
            $params[DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW]->setValue($newVal);
        }

        // if empty value isn't overwritten
        if (strlen($params[DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW]->getValue()) == 0) {
            $newVal = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $paths->uploads); // if empty is generate automatically on ctrl params s0
            $params[DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW]->setValue($newVal);
        }

        // if empty value isn't overwritten
        if (strlen($params[DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW]->getValue()) == 0) {
            $newVal = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $paths->plugins); // if empty is generate automatically on ctrl params s0
            $params[DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW]->setValue($newVal);
        }

        // if empty value isn't overwritten
        if (strlen($params[DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW]->getValue()) == 0) {
            $newVal = DUPX_ArchiveConfig::getNewSubString($oldMainPath, $newMainPath, $paths->muplugins); // if empty is generate automatically on ctrl params s0
            $params[DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW]->setValue($newVal);
        }

        // if empty value isn't overwritten
        if (strlen($params[DUPX_Paramas_Manager::PARAM_SITE_URL]->getValue()) == 0) {
            $newVal = DUPX_ArchiveConfig::getNewSubUrl($oldHomeUrl, $newHomeUrl, $oldSiteUrl); // if empty is generate automatically on ctrl params s0
            $params[DUPX_Paramas_Manager::PARAM_SITE_URL]->setValue($newVal);
        }

        // if empty value isn't overwritten
        if (strlen($params[DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW]->getValue()) == 0) {
            $newVal = DUPX_ArchiveConfig::getNewSubUrl($oldHomeUrl, $newHomeUrl, $oldContentUrl); // if empty is generate automatically on ctrl params s0
            $params[DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW]->setValue($newVal);
        }

        // if empty value isn't overwritten
        if (strlen($params[DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW]->getValue()) == 0) {
            $newVal = DUPX_ArchiveConfig::getNewSubUrl($oldHomeUrl, $newHomeUrl, $oldUploadUrl); // if empty is generate automatically on ctrl params s0
            $params[DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW]->setValue($newVal);
        }

        // if empty value isn't overwritten
        if (strlen($params[DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW]->getValue()) == 0) {
            $newVal = DUPX_ArchiveConfig::getNewSubUrl($oldHomeUrl, $newHomeUrl, $oldPluginsUrl); // if empty is generate automatically on ctrl params s0
            $params[DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW]->setValue($newVal);
        }

        // if empty value isn't overwritten
        if (strlen($params[DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW]->getValue()) == 0) {
            $newVal = DUPX_ArchiveConfig::getNewSubUrl($oldHomeUrl, $newHomeUrl, $oldMuPluginsUrl); // if empty is generate automatically on ctrl params s0
            $params[DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW]->setValue($newVal);
        }
    }
}