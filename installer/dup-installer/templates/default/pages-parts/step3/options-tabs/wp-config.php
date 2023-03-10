<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<div class="help-target">
    <?php DUPX_View_Funcs::helpIconLink('step3'); ?>
</div>
<div class="hdr-sub3">WP-config File Settings</div>
<div  class="dupx-opts s3-opts">
    <?php
    if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
        ?>
        <p>
            <small>Restore backup mode is active so the search and replace option are disabled.</small>
        </p>
        <?php
    } else {
        ?>
        <p>
            See the <a href="https://wordpress.org/support/article/editing-wp-config-php/" target="_blank">WordPress documentation for more information</a>.
        </p>
        <div class="hdr-sub3">Posts/Pages</div>
        <?php
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_EDIT);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_DISALLOW_FILE_MODS);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOSAVE_INTERVAL);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_POST_REVISIONS);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_EMPTY_TRASH_DAYS);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_IMAGE_EDIT_OVERWRITE);
        ?>
        <div class="hdr-sub3 margin-top">Security</div>
        <?php
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_FORCE_SSL_ADMIN);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_AUTOMATIC_UPDATER_DISABLED);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_AUTO_UPDATE_CORE);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_GEN_WP_AUTH_KEY);
        ?>
        <div class="hdr-sub3 margin-top">System/General</div>
        <?php
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CACHE);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_LOG);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DISABLE_FATAL_ERROR_HANDLER);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_DEBUG_DISPLAY);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_SCRIPT_DEBUG);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_CONCATENATE_SCRIPTS);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_SAVEQUERIES);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_ALTERNATE_WP_CRON);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_DISABLE_WP_CRON);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_CRON_LOCK_TIMEOUT);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_COOKIE_DOMAIN);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MEMORY_LIMIT);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WP_MAX_MEMORY_LIMIT);
        ?>
        <div class="hdr-sub3 margin-top">Other Settings</div>
        <?php
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONF_WPCACHEHOME);
    }
    ?>
</div>