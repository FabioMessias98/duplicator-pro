<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* passed values */
/* @var $recoverPackage DUP_PRO_Package_Recover */
/* @var $recoverPackageId int */
/* @var $recoveablePackages array */
/* @var $selector bool */
/* @var $subtitle string */
/* @var $displayCopyLink bool */
/* @var $displayCopyButton bool */
/* @var $displayLaunch bool */
/* @var $displayDownload bool */
/* @var $displayInfo bool */
/* @var $viewMode string */
/* @var $importFailMessage string */

switch ($viewMode) {
    case DUP_PRO_CTRL_recovery::VIEW_WIDGET_NO_PACKAGE_SET:
        ?>
        <div class="margin-bottom-1">
            <?php
            DUP_PRO_U::_e(
                'The Recovery Point allows one to quickly restore the site to a prior state. '
                .'To use this, mark a package as the Recovery Point, then copy and save off the associated URL. '
                .'Then, if a problem occurs, browse to the URL to launch a streamlined installer to quickly restore the site.');
            ?>
        </div>
        <?php
        break;
    case DUP_PRO_CTRL_recovery::VIEW_WIDGET_NOT_VALID:
        ?>
        <div class="orangered margin-bottom-1">
            <?php echo esc_html($importFailMessage); ?>
        </div>
        <?php
        break;
    case DUP_PRO_CTRL_recovery::VIEW_WIDGET_VALID:
        $downloadLauncherData = array(
            'fileName'    => $recoverPackage->getLauncherFileName(),
            'fileContent' => DupProSnapLibIOU::getInclude(dirname(__FILE__).'/recovery-download-launcher-content.php', array(
                'recoverPackage' => $recoverPackage
            ))
        );
        ?>
        <div class="dup-pro-recovery-active-link-wrapper" >
            <div class="dup-pro-recovery-active-link-header" >
                <i class="fas fa-undo-alt main-icon <?php echo $bkgClass; ?>"></i>
                <div class="main-title" >
                    <?php DUP_PRO_U::_e('Recovery point is ready to use.'); ?>
                </div>
                <div class="main-subtitle margin-bottom-1" >
                    <b><?php DUP_PRO_U::_e('Status:') ?></b> 
                    <?php if ($recoverPackage->isOutToDate()) { ?>
                        <span class="red"><?php DUP_PRO_U::_e('out of date') ?></span>
                    <?php } else { ?>
                        <span class="green"><?php DUP_PRO_U::_e('ready') ?></span>
                    <?php } ?>
                </div>
            </div>
            <?php echo $subtitle; ?>
            <?php if ($displayInfo) { ?>
                <div class="dup-pro-recovery-package-info margin-bottom-1" >
                    <table>
                        <tbody>
                            <tr>
                                <td><?php DUP_PRO_U::_e('Name'); ?>:</td>
                                <td><b><?php echo esc_html($recoverPackage->getPackageName()); ?></b></td>
                            </tr>
                            <tr>
                                <td><?php DUP_PRO_U::_e('Date'); ?>:</td>
                                <td><b><?php echo esc_html($recoverPackage->getCreated()); ?></b></td>
                            </tr>
                            <tr>
                                <td><?php DUP_PRO_U::_e('Age'); ?>:</td>
                                <td>
                                    <b><?php printf(DUP_PRO_U::__('Created %s hour(s) ago.'), $recoverPackage->getPackageLife()); ?></b> 
                                    <i><?php DUP_PRO_U::_e('All changes made after package creation will be lost.'); ?></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
        break;
    default:
        ?>
        <p class="orangered">
            <?php echo DUP_PRO_U::__('Invalid view mode.'); ?>
        </p>
    <?php
}
