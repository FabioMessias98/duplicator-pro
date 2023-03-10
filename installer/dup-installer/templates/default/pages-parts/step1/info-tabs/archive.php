<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* Variables */
/* @var $checkArchvie DUPX_Validation_test_archive_check */

$archiveConfig = DUPX_ArchiveConfig::getInstance();
?>
<div id="tabs-1">
    <table class="s1-archive-local">
        <tr>
            <td colspan="2"><div class="hdr-sub3">Site Details</div></td>
        </tr>
        <tr>
            <td>Site:</td>
            <td><?php echo DUPX_U::esc_html($archiveConfig->blogname); ?> </td>
        </tr>
        <tr>
            <td>Notes:</td>
            <td><?php echo strlen($archiveConfig->package_notes) ? "{$archiveConfig->package_notes}" : " - no notes - "; ?></td>
        </tr>
        <?php if ($archiveConfig->exportOnlyDB) : ?>
            <tr>
                <td>Mode:</td>
                <td>Archive only database was enabled during package package creation.</td>
            </tr>
        <?php endif; ?>
    </table>

    <table class="s1-archive-local">
        <tr>
            <td colspan="2"><div class="hdr-sub3">File Details</div></td>
        </tr>
        <tr>
            <td>Size:</td>
            <td><?php echo DUPX_U::readableByteSize(DUPX_Conf_Utils::archiveSize()); ?> </td>
        </tr>
        <tr>
            <td>Path:</td>
            <td><?php echo DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW); ?> </td>
        </tr>
        <tr>
            <td>Archive:</td>
            <td><?php echo DUPX_ArchiveConfig::getInstance()->package_name; ?> </td>
        </tr>
        <tr>
            <td style="vertical-align:top">Status:</td>
            <td>
                <?php echo $checkArchvie->getContent(); ?>
            </td>
        </tr>
    </table>
</div>