<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?><script>
    DupPro.Pack.SetRecoveryPoint = function (packageId, callbackOnSuccess, callbackOnError, topHeaderMessage) {
        topHeaderMessage = (typeof topHeaderMessage !== 'undefined') ? topHeaderMessage : true;

        let okMsgContent = <?php echo json_encode(DupProSnapLibIOU::getInclude(dirname(__FILE__).'/recovery-message-set-ok.php')); ?>;
        let errorMsgContent = <?php echo json_encode(DupProSnapLibIOU::getInclude(dirname(__FILE__).'/recovery-message-set-error.php')); ?>;

        DupPro.Pack.removeRecoveryMessages();
        DupPro.StandarJsonAjaxWrapper({
            action: 'duplicator_pro_set_recovery',
            recovery_package: packageId,
            fromPageTab: <?php echo json_encode(DUP_PRO_CTRL_Base::getCurrentPageTabId()); ?>,
            nonce: '<?php echo wp_create_nonce('duplicator_pro_set_recovery'); ?>'
        },
                function (funcData, data, textStatus, jqXHR) {
                    if (topHeaderMessage) {
                        DupPro.addAdminMessage(okMsgContent, 'notice', {
                            updateCallback: function (msgNode) {
                                msgNode.find('.recovery-set-message-ok').html(funcData.adminMessage);
                                DupPro.UI.loadQtip();
                                msgNode.find('[data-download-laucher]').click(function () {
                                    let data = jQuery(this).data('download-laucher');
                                    DupPro.downloadContentAsfile(data.fileName, data.fileContent, 'text/html');
                                });
                            }
                        });
                    }

                    if (typeof callbackOnSuccess === "function") {
                        callbackOnSuccess(funcData, data, textStatus, jqXHR);
                    }

                    return '';
                },
                function (funcData, data, textStatus, jqXHR) {
                    DupPro.addAdminMessage(errorMsgContent, 'error', {
                        'updateCallback': function (msgNode) {
                            msgNode.find('.recovery-error-message').text(data.message);
                        }
                    });

                    if (typeof callbackOnError === "function") {
                        callbackOnError(funcData, data, textStatus, jqXHR);
                    }

                    return '';
                }
        );
    };

    DupPro.Pack.ResetRecoveryPoint = function (callbackOnSuccess) {
        let okMsgContent = <?php echo json_encode(DupProSnapLibIOU::getInclude(dirname(__FILE__).'/recovery-message-reset-ok.php')); ?>;
        let errorMsgContent = <?php echo json_encode(DupProSnapLibIOU::getInclude(dirname(__FILE__).'/recovery-message-reset-error.php')); ?>;

        DupPro.Pack.removeRecoveryMessages();
        DupPro.StandarJsonAjaxWrapper({
            action: 'duplicator_pro_reset_recovery',
            nonce: '<?php echo wp_create_nonce('duplicator_pro_reset_recovery'); ?>',
            fromPageTab: <?php echo json_encode(DUP_PRO_CTRL_Base::getCurrentPageTabId()); ?>,
        },
                function (funcData, data, textStatus, jqXHR) {
                    DupPro.addAdminMessage(okMsgContent, 'notice');

                    if (typeof callbackOnSuccess === "function") {
                        callbackOnSuccess(funcData, data, textStatus, jqXHR);
                    }

                    return '';
                },
                function (funcData, data, textStatus, jqXHR) {
                    DupPro.addAdminMessage(errorMsgContent, 'error', {
                        'updateCallback': function (msgNode) {
                            msgNode.find('.recovery-error-message').text(data.message);
                        }
                    });
                    return '';
                }
        );
    };

    DupPro.Pack.UpdatgeRecoveryWidget = function (callbackOnSuccess) {
        let okMsgContent = <?php echo json_encode(DupProSnapLibIOU::getInclude(dirname(__FILE__).'/recovery-message-reset-ok.php')); ?>;
        let errorMsgContent = <?php echo json_encode(DupProSnapLibIOU::getInclude(dirname(__FILE__).'/recovery-message-reset-error.php')); ?>;

        DupPro.Pack.removeRecoveryMessages();
        DupPro.StandarJsonAjaxWrapper({
            action: 'duplicator_pro_get_recovery_widget',
            nonce: '<?php echo wp_create_nonce('duplicator_pro_get_recovery_widget'); ?>',
            fromPageTab: <?php echo json_encode(DUP_PRO_CTRL_Base::getCurrentPageTabId()); ?>,
        },
                function (funcData, data, textStatus, jqXHR) {
                    if (typeof callbackOnSuccess === "function") {
                        callbackOnSuccess(funcData, data, textStatus, jqXHR);
                    }
                    return '';
                },
                function (funcData, data, textStatus, jqXHR) {
                    return <?php json_encode(DUP_PRO_U::__('Can\'t update recovery widget')); ?>;
                }
        );
    };

    DupPro.Pack.removeRecoveryMessages = function () {
        jQuery('#wpcontent .dup-pro-recovery-message').closest('.notice').remove();
    };

    DupPro.Pack.SetRecoveryPackageDetails = function (wrapper, details, setColor) {
        const setDelayAnimation = 1000;
        const setDurationAnimationStart = 500;
        const setDurationAnimationEnd = 1000;

        let newDetails = jQuery(details);
        wrapper.replaceWith(newDetails);
        wrapper = newDetails;

        wrapper.find('.dup-pro-recovery-point-selector-area select, .dup-pro-recovery-point-actions .copy-link')
                .stop()
                .animate({
                    backgroundColor: setColor
                }, setDurationAnimationStart)
                .delay(setDelayAnimation)
                .animate({
                    backgroundColor: "transparent"
                }, setDurationAnimationEnd);

        wrapper.find('.dup-pro-recovery-point-details')
                .stop()
                .css({
                    'outline': '5px solid transparent',
                    'outline-offset': '5px'
                })
                .animate({
                    outlineColor: setColor
                }, setDurationAnimationStart)
                .delay(setDelayAnimation)
                .animate({
                    outlineColor: "transparent",
                    'outline-width': '0',
                    'outline-offset': '0'
                }, setDurationAnimationEnd);

        DupPro.Pack.initRecoveryWidget(wrapper);
    };

    DupPro.Pack.initRecoveryWidget = function (widgetWrapper) {
        widgetWrapper.find('.recovery-reset').off().click(function () {
            DupPro.Pack.ResetRecoveryPoint(function (funcData, data, textStatus, jqXHR) {
                widgetWrapper.find('.recovery-select').val('');
                DupPro.Pack.SetRecoveryPackageDetails(widgetWrapper, funcData.packageDetails, '#e1f5c1');
            });
        });

        widgetWrapper.find('.recovery-set').off().click(function () {
            let packageId = widgetWrapper.find('.recovery-select').val();
            if (!packageId) {
                DupPro.Pack.ResetRecoveryPoint(function (funcData, data, textStatus, jqXHR) {
                    DupPro.Pack.SetRecoveryPackageDetails(widgetWrapper, funcData.packageDetails, '#e1f5c1');
                });
            } else {
                DupPro.Pack.SetRecoveryPoint(packageId,
                        function (funcData, data, textStatus, jqXHR) {
                            DupPro.Pack.SetRecoveryPackageDetails(widgetWrapper, funcData.packageDetails, '#e1f5c1');
                        },
                        function (funcData, data, textStatus, jqXHR) {
                            widgetWrapper.find('.recovery-select').val('');
                            DupPro.Pack.SetRecoveryPackageDetails(widgetWrapper, '<p class="red" >' + data.message + '</span>', '#fcc3bd');
                        },
                        false);
            }
        });

        DupPro.UI.loadQtip();

        widgetWrapper.find('.dup-pro-recovery-windget-refresh').off().click(function () {
            DupPro.Pack.UpdatgeRecoveryWidget(function (funcData, data, textStatus, jqXHR) {
                DupPro.Pack.SetRecoveryPackageDetails(widgetWrapper, funcData.widget, '#e1f5c1');
            });
        });

        widgetWrapper.find('[data-download-laucher]').off().click(function () {
            let data = jQuery(this).data('download-laucher');
            DupPro.downloadContentAsfile(data.fileName, data.fileContent, 'text/html');
        });
    };

    jQuery(document).ready(function ($)
    {
        $('.dup-pro-recovery-widget-wrapper').each(function () {
            let widgetWrapper = jQuery(this);
            DupPro.Pack.initRecoveryWidget(widgetWrapper);
        });

        $('.dup-pro-open-help-link').click(function () {
            event.stopPropagation();
            let helpLink = $('#contextual-help-link');
            $("html, body").animate({scrollTop: 0}, "fast");

            if (helpLink.hasClass('screen-meta-active')) {
                return;
            }

            helpLink.trigger('click');
        });
    });
</script>
