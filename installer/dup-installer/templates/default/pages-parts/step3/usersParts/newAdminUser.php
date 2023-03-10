<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager  = DUPX_Paramas_Manager::getInstance();
$title = DUPX_MU::newSiteIsMultisite() ? 'New SUPER ADMIN account' : 'New Admin Account';
?>
<div class="hdr-sub3 margin-top-2"><?php echo $title; ?></div>
<p style="text-align: center">
    <i style="color:gray;font-size: 11px">
        This feature is optional.  If the username already exists the account will NOT be created or updated.
    </i>
</p>
<div class="dupx-opts s3-opts">
    <?php
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_ADMIN_CREATE_NEW);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_ADMIN_NAME);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_ADMIN_PASSWORD);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_ADMIN_MAIL);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_ADMIN_NICKNAME);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_ADMIN_FIRST_NAME);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_WP_ADMIN_LAST_NAME);
    ?>
</div>