<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager  = DUPX_Paramas_Manager::getInstance();
$nManager       = DUPX_NOTICE_MANAGER::getInstance();
?>
<ul class="final-review-actions" >
    <li>
        <a href="javascript:void(0)" onclick="$('#s4-install-report').toggle(400)">Review Migration Report</a>
    </li>
    <li>
        Review this site's <a href="<?php echo DUPX_U::esc_url($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW)); ?>" target="_blank">front-end</a> or
        re-run the installer and <span class="link-style" data-go-step-one-url="<?php echo DupProSnapLibURLU::urlEncodeAll(DUPX_CSRF::getVal('installerOrigCall')); ?>" >go back to step 1</span>.
    </li>
    <li>
        <?php
        $wpconfigNotice = $nManager->getFinalReporNoticeById('wp-config-changes');
        $htaccessNorice = $nManager->getFinalReporNoticeById('htaccess-changes');
        ?>
        Please validate <?php echo $wpconfigNotice->longMsg; ?> and <?php echo $htaccessNorice->longMsg; ?>.</li>
    <li>
        For additional help and questions visit the <a href='http://snapcreek.com/support/docs/faqs/' target='_blank'>online FAQs</a>.
    </li>
</ul>
