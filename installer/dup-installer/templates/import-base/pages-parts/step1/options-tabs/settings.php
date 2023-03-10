<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();

if (DUPX_Conf_Utils::showMultisite()) {
    ?> 
    <div class="hdr-sub3">Multisite options</div>
    <?php
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_MULTISITE_INST_TYPE);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);
    ?>
    <?php
}