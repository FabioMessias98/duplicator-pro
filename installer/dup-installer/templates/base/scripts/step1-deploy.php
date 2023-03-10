<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$nextStepPrams = array(
    DUPX_Paramas_Manager::PARAM_CTRL_ACTION => 'ctrl-step4',
    DUPX_Security::CTRL_TOKEN               => DUPX_CSRF::generate('ctrl-step4')
);
?>
<script>
    $('#advanced-toggle-info').click(function () {
        let info = $('#advanced-mode-info');
        if (info.hasClass('no-display')) {
            info.removeClass('no-display');
        } else {
            info.addClass('no-display');
        }
    });

    DUPX.deployStep1 = function () {
        let step1Form = $('#s1-input-form');

        DUPX.sendParamsStep1(step1Form, function () {
            DUPX.startAjaxExtraction(true, function () {
                DUPX.startAjaxDbInstall(true, function () {
                    DUPX.siteProcessingReplaceData(true, function () {
                        DUPX.finalTests.test(function () {
                            DUPX.redirect(DUPX.dupInstallerUrl, 'post', <?php echo DupProSnapJsonU::wp_json_encode($nextStepPrams); ?>);
                        });
                    });
                });
            });
        });
    };
</script>