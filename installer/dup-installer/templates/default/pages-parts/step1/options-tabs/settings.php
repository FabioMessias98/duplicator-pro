<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<div class="hdr-sub3">Site Details</div>
<?php
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_URL_NEW);
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PATH_NEW);
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_ARCHIVE_ACTION);
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE);

if (DUPX_Conf_Utils::showMultisite()) {
    ?> 
    <div class="hdr-sub3 margin-top-2">Multisite options</div>
    <?php
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);
    ?>
    <?php
}