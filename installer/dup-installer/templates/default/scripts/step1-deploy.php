<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$nextStepPrams = array(
    DUPX_Paramas_Manager::PARAM_CTRL_ACTION => 'ctrl-step2',
    DUPX_Security::CTRL_TOKEN               => DUPX_CSRF::generate('ctrl-step2')
);
?>
<script>
    DUPX.deployStep1 = function () {
        let step1Form = $('#s1-input-form');

        DUPX.sendParamsStep1(step1Form, function () {
            DUPX.startAjaxExtraction(true, function (data) {
                DUPX.redirect(DUPX.dupInstallerUrl, 'post', <?php echo DupProSnapJsonU::wp_json_encode($nextStepPrams); ?>);
            });
        });
    };
</script>