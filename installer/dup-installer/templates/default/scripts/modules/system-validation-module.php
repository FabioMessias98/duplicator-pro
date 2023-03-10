<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<script>
    const validateAction = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::ACTION_VALIDATE); ?>;
    const validateToken = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::generateToken(DUPX_Ctrl_ajax::ACTION_VALIDATE)); ?>;

    DUPX.initialValidateAction = function (validateCallback, showContentOnResult, resetTopMessage) {
        if (resetTopMessage) {
            DUPX.pageComponents.resetTopMessages();
        }
        DUPX.pageComponents.showProgress({
            'title': 'System validation',
            'bottomText':
                    '<i>Keep this window open during the validation process.</i><br/>' +
                    '<i>This can take several minutes.</i>'
        });

        DUPX.StandarJsonAjaxWrapper(
                validateAction,
                validateToken,
                {},
                function (data) {
                    if (showContentOnResult) {
                        DUPX.pageComponents.showContent();
                    }
                    if (typeof validateCallback === "function") {
                        validateCallback(data.actionData);
                    } else {
                        alert('Validate ' + data.actionData.mainText);
                    }
                },
                DUPX.ajaxErrorDisplayRestart
                );
    };

    DUPX.setValidationBadge = function (selector, newClass) {
        let item = $(selector);
        if (!item.length) {
            return;
        }
        item.removeClass('wait fail warn good pass')
        if (newClass) {
            item.addClass(newClass);
        }
    };
</script>