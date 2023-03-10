<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
$title         = DUPX_MU::newSiteIsMultisite() ? 'Super Admin' : 'Admin';
?>
<div class="hdr-sub3">Existing <?php echo $title; ?> Password Reset</div>
<div class="dupx-opts s3-opts">
    <?php
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_USERS_PWD_RESET);
    ?>
</div>