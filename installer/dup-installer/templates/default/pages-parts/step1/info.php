<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$hostManager     = DUPX_Custom_Host_Manager::getInstance();
$isRestoreBackup = DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_BK_RESTORE;
$opened          = $hostManager->isManaged() || $isRestoreBackup;
$checkArchvie    = new DUPX_Validation_test_archive_check();
?>
<div class="hdr-sub1 toggle-hdr <?php echo $opened ? 'close' : 'open'; ?>" data-type="toggle" data-target="#s1-area-archive-file">
    <a id="s1-area-archive-file-link"><i class="fa fa-plus-square"></i>Info</a>
    <span class="status-badge right <?php echo $checkArchvie->getBadgeClass(); ?>"></span>
</div>
<div id="s1-area-archive-file" class="hdr-sub1-area tabs-area <?php echo $opened ? '' : 'no-display'; ?>" >
    <div class="tabs">
        <ul>
            <?php if ($isRestoreBackup) { ?>
                <li><a href="#tabs-3" >Restore backup</a></li>
                <?php
            }

            if ($hostManager->isManaged()) {
                ?>
                <li><a href="#tabs-2" >Managed Hosting</a></li>
            <?php } ?>
            <li><a href="#tabs-1">Archive</a></li>

        </ul>
        <?php
        dupxTplRender('pages-parts/step1/info-tabs/managed-hosting');
        dupxTplRender('pages-parts/step1/info-tabs/restore-backup');
        dupxTplRender('pages-parts/step1/info-tabs/archive', array('checkArchvie' => $checkArchvie));
        ?>
    </div>
</div>