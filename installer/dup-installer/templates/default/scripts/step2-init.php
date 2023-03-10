<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();

$nextStepPrams = array(
    DUPX_Paramas_Manager::PARAM_CTRL_ACTION => 'ctrl-step3',
    DUPX_Security::CTRL_TOKEN               => DUPX_CSRF::generate('ctrl-step3')
);
?><script>
    DUPX.runDeployment = function () {
        //Validate input data
        var formInput = $('#s2-input-form');

        DUPX.sendParamsStep2(formInput, function () {
            DUPX.startAjaxDbInstall(true, function () {
                DUPX.redirect(DUPX.dupInstallerUrl, 'post', <?php echo DupProSnapJsonU::wp_json_encode($nextStepPrams); ?>);
            });
        });
    };
</script>
