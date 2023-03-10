<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
$archiveConfig = DUPX_ArchiveConfig::getInstance();
?>
<div class="help-target">
    <?php DUPX_View_Funcs::helpIconLink('step1'); ?>
</div>
<?php dupxTplRender('pages-parts/step1/options-tabs/engine-settings'); ?>
<div class="hdr-sub3 margin-top-2">Processing</div>  
<?php
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SAFE_MODE);
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_FILE_TIME);
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_LOGGING);
if (!DupProSnapLibOSU::isWindows()) {
    ?>
    <div class="param-wrapper" >
        <?php $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SET_FILE_PERMS); ?>
        &nbsp;
        <?php $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_FILE_PERMS_VALUE); ?>
    </div>
    <div class="param-wrapper" >
        <?php $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SET_DIR_PERMS); ?>
        &nbsp;
        <?php $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DIR_PERMS_VALUE); ?>
    </div>
    <?php
}
if (!$archiveConfig->exportOnlyDB) {
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_REMOVE_RENDUNDANT);
}
?>
<div class="hdr-sub3 margin-top-2">Configuration files</div>  
<?php
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_CONFIG);
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG);
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_OTHER_CONFIG);
