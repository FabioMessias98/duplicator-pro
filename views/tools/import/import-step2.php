<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$postTypeCount = DUP_PRO_WP_U::getPostTypesCount();
?>
<div class="dup-pro-import-header" >
    <h2 class="title">
        <i class="fas fa-arrow-alt-circle-down"></i> <?php printf(DUP_PRO_U::esc_html__("Step %s of 2: Archive Confirmation"), '<span class="red" >2</span>'); ?>
    </h2>
    <hr />
</div>
<div class="dup-pro-recovery-details-max-width-wrapper" >
    <p class="dup-pro-import-confirm-question red">
        <i class="fas fa-exclamation-triangle fa-sm"></i>
        <?php DUP_PRO_U::esc_html_e("Warning: This operation will overwrite this entire WordPress site!") ?>
    </p>

    <div class="dup-pro-import-box closable opened" >
        <div class="box-title" >
            <?php DUP_PRO_U::_e('Recovery Point'); ?>
        </div>
        <div class="box-content">
            <div  id="dup-pro-recovery-details-select-entry" class="dup-pro-recovery-info-set" >
                <?php
                DUP_PRO_CTRL_recovery::renderRecoveryWidged(array(
                    'selector'   => true,
                    'subtitle'   => '',
                    'copyLink'   => true,
                    'copyButton' => true,
                    'launch'     => false,
                    'download'   => false,
                    'info'       => true
                ));
                ?>
            </div>
            <hr>

            <div class="dup-pro-recovery-not-required">
                <i class="far fa-arrow-alt-circle-right"></i>
                <?php DUP_PRO_U::_e('The Recovery Point is not mandatory to perform an import. However, it can assist in restoring this site if there is a problem during install. '
                    . ' If you have no need to recover this site then you can continue without creating the Recovery Point.');
                ?>
            </div>
            
        </div>
    </div>

    <div class="dup-pro-import-box closable closed" >
        <div class="box-title" >
            <?php DUP_PRO_U::_e('System Overview'); ?>
        </div>
        <div class="box-content">
            <div id="dup-pro-recovery-details-overview" >
                <div>
                    <?php DUP_PRO_U::esc_html_e("This site currently contains"); ?>:
                </div>

                <table class="margin-left-2" >
                    <?php foreach ($postTypeCount as $label => $count) { ?>
                        <tr>
                            <td><?php echo esc_html($label); ?></td>
                            <td class="text-right"><?php echo $count; ?></td>
                        </tr>
                    <?php } ?>
                </table>

                <p>
                    <?php DUP_PRO_U::esc_html_e("This feature should ONLY be used on sites that you no longer need.") ?>
                </p>

                <?php DUP_PRO_U::esc_html_e("This process will:") ?>
                <ul>
                    <li><i class="far fa-check-circle"></i> <?php DUP_PRO_U::esc_html_e("Completely delete the files and database this WordPress site is using."); ?></li>
                    <li><i class="far fa-check-circle"></i> <?php DUP_PRO_U::esc_html_e("Launch the interactive installer wizard to install this new package."); ?></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="dup-pro-import-confirm-buttons">
        <input id="dup-pro-import-launch-installer-cancel" type="button" class="button button-large recovery-reset" value="Cancel" >
        <input id="dup-pro-import-launch-installer-confirm" type="button" class="button button-primary button-large" value="Continue &amp; Overwrite" onclick="DupPro.ImportManager.confirmLaunchInstaller();">
    </div>
</div>
