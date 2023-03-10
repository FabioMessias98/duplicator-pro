<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (DUPX_InstallerState::getInstance()->getMode() !== DUPX_InstallerState::MODE_BK_RESTORE) {
    return;
}
?>
<div id="tabs-3">
    <h3>Restore backup installation</h3>
    <p>
        By running this installation all the site data will be lost and the current backup restored.
        If you do not wish to continue it is still possible to close this window to interrupt the restore.
        <br><br>
        <b>Continuing, it will no longer be possible to go back.</b>
    </p>
</div>
<?php
