<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<div class="hdr-sub3">Engine Settings</div>
<?php
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE);
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DB_ENGINE);
