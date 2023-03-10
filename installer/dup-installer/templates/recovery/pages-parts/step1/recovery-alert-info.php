<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$this->importSiteInfo = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_FROM_SITE_IMPORT_INFO);
$packageLife          = isset($this->importSiteInfo['recovery_package_life']) ? $this->importSiteInfo['recovery_package_life'] : 0;
$created              = DUPX_ArchiveConfig::getInstance()->created;
?><div class="generic-box" >
    <div class="box-title" >
        <i class="fas fa-exclamation-triangle"></i>Recovery Site Info
    </div>
    <div class="box-content" >
        <div class="recovery-main-info red" >
            This installer is about to overwrite the current data in this site with data from the Recovery Point 
            created on <b><?php echo $created; ?></b> which is <b><?php echo $packageLife; ?> hour(s) old</b>.
        </div>
    </div>
</div>