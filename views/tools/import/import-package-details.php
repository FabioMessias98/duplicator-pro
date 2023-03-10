<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* passed values */
/* @var $importObj DUP_PRO_Package_Importer */

if (!$importObj instanceof DUP_PRO_Package_Importer) {
    return;
}
?>
<div class="dup-pro-import-package-detail-content" >
    <?php
    if (!$importObj->isImportable($importFailMessage)) {
        ?>
        <p class="orangered">
            <?php echo $importFailMessage; ?>
        </p>
        <?php
    } else {
        ?>
        <p class="green">
            <b><?php DUP_PRO_U::_e('Package is ready to install.'); ?></b>
        </p>
        <?php
    }

    if ($importObj->isValid()) {
        ?>
        <ul>
            <li>
                <span class="label title"><?php DUP_PRO_U::_e('Site Details:'); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Home url'); ?>:</span>
                <span class="value"><?php echo esc_html($importObj->getHomeUrl()); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Home path'); ?>:</span>
                <span class="value"><?php echo esc_html($importObj->getHomePath()); ?></span>
            </li>
            <li>
                <span class="label title"><?php DUP_PRO_U::_e('Version Details:'); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Duplicator'); ?>:</span>
                <span class="value"><?php echo esc_html($importObj->getDupVersion()); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Wordpress'); ?>:</span>
                <span class="value"><?php echo esc_html($importObj->getWPVersion()); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('PHP'); ?>:</span>
                <span class="value"><?php echo esc_html($importObj->getPhpVersion()); ?></span>
            </li>
            <li>
                <span class="label title"><?php DUP_PRO_U::_e('Archive Details:'); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Created'); ?>:</span>
                <span class="value"><?php echo esc_html($importObj->getCreated()); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Size'); ?>:</span>
                <span class="value"><?php echo esc_html(DUP_PRO_U::byteSize($importObj->getSize())); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Folders'); ?>:</span>
                <span class="value"><?php echo esc_html(number_format($importObj->getNumFolders())); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Files'); ?>:</span>
                <span class="value"><?php echo esc_html(number_format($importObj->getNumFiles())); ?></span>
            </li>
            <li>
                <span class="label title"><?php DUP_PRO_U::_e('Database Details:'); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Size'); ?>:</span>
                <span class="value"><?php echo esc_html(DUP_PRO_U::byteSize($importObj->getDbSize())); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Tables'); ?>:</span>
                <span class="value"><?php echo esc_html($importObj->getNumTables()); ?></span>
            </li>
            <li>
                <span class="label"><?php DUP_PRO_U::_e('Rows'); ?>:</span>
                <span class="value"><?php echo esc_html(number_format($importObj->getNumRows())); ?></span>
            </li>
        </ul>
    <?php } ?>
</div>
