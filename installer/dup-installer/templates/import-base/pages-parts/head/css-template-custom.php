<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$importSiteInfo = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_FROM_SITE_IMPORT_INFO);
if (isset($importSiteInfo['color-scheme'])) {
    $colorScheme = $importSiteInfo['color-scheme'];
} else {
    $colorScheme           = array();
    $colorScheme['colors'] = array('#222', '#333', '#0073aa', '#00a0d2');
}
$colorPrimaryButton = isset($importSiteInfo['color-primary-button']) ? $importSiteInfo['color-primary-button'] : $colorScheme->colors[2];
?>
<style>
    body.template_import-base {
        background: transparent;
    }

    .template_import-base #page-top-messages {
        max-width: 800px;
    }

    .template_import-base #content {
        border: 0 none;
        margin: 0;
        border-radius: 0;
        box-shadow: none;
        max-width: none;
        width: 100%;
    }

    .template_import-base #content-inner {
        margin: 0 20px 40px 0;
    }

    .template_import-base .main-form-content {
        min-height: 0;
    }

    .template_import-base #main-content-wrapper {
        max-width: 800px;
    }

    .template_import-base .sub-header,
    .template_import-base #header-main-wrapper .dupx-logfile-link {
        font-size: 12px;
    }

    .template_import-base .generic-box,
    .template_import-base .hdr-sub1,
    .template_import-base .hdr-sub1-area {
        border-radius: 0;
        border-color: #e5e5e5;
        background: #FFF;
    }

    .template_import-base .generic-box .box-title,
    .template_import-base .hdr-sub1 {
        font-size: 16px;
        font-weight: bold;
        padding: 8px 12px;
        background: #FFF
    }

    .template_import-base  #validation-result .category-wrapper {
        border-radius: 0;
    }

    .template_import-base #validation-result .category-wrapper > .header {
        background: #EFEFEF;
    }

    .template_import-base #validation-result .test-title {
        background: #F3F3F3;
    }

    .template_import-base .default-btn {
        background: <?php echo $colorPrimaryButton; ?>;
        border-color: <?php echo $colorPrimaryButton; ?>;
        color: #fff;
        text-decoration: none;
        text-shadow: none;
    }

    .template_import-base .default-btn, 
    .template_import-base .secondary-btn {
        display: inline-block;
        text-decoration: none;
        font-size: 13px;
        line-height: 32px;
        min-height: 32px;
        margin: 0;
        margin-left: 0px;
        padding: 0 12px;
        cursor: pointer;
        border-width: 1px;
        border-style: solid;
        -webkit-appearance: none;
        border-radius: 3px;
        white-space: nowrap;
        box-sizing: border-box;
    }

    .template_import-base .default-btn:hover {
        background: <?php echo $colorScheme['colors'][3]; ?>;
        border-color: <?php echo $colorScheme['colors'][3]; ?>;
        color: #fff;
    }

    .template_import-base .default-btn.disabled,
    .template_import-base .default-btn:disabled,
    .template_import-base .secondary-btn.disabled,
    .template_import-base .secondary-btn:disabled  {
        color:silver;         
        background-color: #f3f5f6;
        border: 1px solid silver;
    }

    .template_import-base .secondary-btn {
        color: black;         
        background-color: #f3f5f6;
        border: 1px solid #7e8993;
    }

    .template_import-base .secondary-btn:hover {
        color: #FEFEFE;         
        background-color: #CFCFCF;
    }

    .template_import-base .ui-widget-overlay {
        background: #f1f1f1;
        opacity: .7;
        filter: Alpha(Opacity=70);
    }

</style>