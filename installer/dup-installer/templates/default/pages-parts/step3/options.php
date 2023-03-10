<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
$wpConfig      = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_WP_CONFIG);
$skipWpConfig  = ($wpConfig == 'nothing' || $wpConfig == 'original');
?>
<!-- ==========================
OPTIONS -->
<div class="hdr-sub1" >
    Options
</div>
<!-- START TABS -->
<div id="s3-adv-opts" class="hdr-sub1-area tabs-area">
    <div id="tabs" class="no-display">
        <ul>
            <li><a href="#tabs-search-rules">Replace</a></li>
            <li><a href="#tabs-admin-account">Admin Account</a></li>
            <li><a href="#tabs-scan-options">Scan Options</a></li>
            <li><a href="#tabs-plugins">Plugins</a></li>
            <?php if (!$skipWpConfig) { ?>
                <li><a href="#tabs-wp-config-file">WP-Config File</a></li>
            <?php } ?>
        </ul>
    
        <!-- =====================
        SEARCH RULES TAB -->
        <div id="tabs-search-rules">
            <?php dupxTplRender('pages-parts/step3/search-rules'); ?>
        </div>
        
        <!-- =====================
        ADMIN TAB -->
        <div id="tabs-admin-account">
            <?php dupxTplRender('pages-parts/step3/options-tabs/users'); ?>
        </div>

        <!-- =====================
        SCAN TAB -->
        <div id="tabs-scan-options">
            <?php dupxTplRender('pages-parts/step3/options-tabs/scan'); ?>
        </div>

        <!-- =====================
        PLUGINS  TAB -->
        <div id="tabs-plugins">
            <?php dupxTplRender('pages-parts/step3/options-tabs/plugins'); ?>
        </div>
        <?php if (!$skipWpConfig) { ?>
            <!-- =====================
            WP-CONFIG TAB -->
            <div id="tabs-wp-config-file">
                <?php dupxTplRender('pages-parts/step3/options-tabs/wp-config'); ?>
            </div>
        <?php } ?>
    </div>
</div>
