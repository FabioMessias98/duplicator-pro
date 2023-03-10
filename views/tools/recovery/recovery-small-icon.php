<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

// @var $recoverPackage DUP_PRO_Package_Recover

if (isset($recoverPackage) && ($recoverPackage instanceof DUP_PRO_Package_Recover)) {
    $copyLink = $recoverPackage->getInstallLink();
} else {
    $copyLink = '';
}
?><span 
    class="dup-pro-recovery-package-small-icon maroon"
    title="<?php DUP_PRO_U::esc_attr_e('This package is set as the Recovery Point'); ?>" 
    data-dup-pro-copy-value="<?php echo $copyLink; ?>"
    data-dup-pro-copy-title="<?php DUP_PRO_U::_e("Copy Recovery URL to clipboard"); ?>"
    data-dup-pro-copied-title="<?php DUP_PRO_U::_e("Recovery URL copied to clipboard"); ?>"
    ><i class="fas fa-undo-alt"></i><span>