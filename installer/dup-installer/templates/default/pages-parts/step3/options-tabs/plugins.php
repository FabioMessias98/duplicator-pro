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
<div class="hdr-sub3">Plugins Settings</div>
<?php
$paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_PLUGINS);
