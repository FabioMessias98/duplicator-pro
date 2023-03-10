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
    const urlNewInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_URL_NEW)); ?>;
    const pathNewInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_PATH_NEW)); ?>;
    const exeSafeModeInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_SAFE_MODE)); ?>;
    const htConfigInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG)); ?>;
    const htConfigWrapperId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormWrapperId(DUPX_Paramas_Manager::PARAM_HTACCESS_CONFIG)); ?>;
    const otConfigInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_OTHER_CONFIG)); ?>;
    const otConfigWrapperId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormWrapperId(DUPX_Paramas_Manager::PARAM_OTHER_CONFIG)); ?>;
    const archiveEngineInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_ARCHIVE_ENGINE)); ?>;
    const validationShowInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_VALIDATION_SHOW_ALL)); ?>;
    const acceptContinueInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_ACCEPT_TERM_COND)); ?>;

    $(document).ready(function () {
        let validateArea = $('#validate-area');
        let validateAreaHeader = $('#validate-area-header');
        let basicSetupAreaHeader = $('#base-setup-area-header');
        let optionsAreaHeader = $('#options-area-header');
        let validateNoResult = validateArea.find('#validate-no-result');
        let stepActions = $('.bottom-step-action');
        let step1Form = $('#s1-input-form');

        DUPX.getManaualArchiveOpt = function ()
        {
            $("html, body").animate({scrollTop: $(document).height()}, 1500);
            $("div[data-target='#s1-area-adv-opts']").find('i.fa').removeClass('fa-plus-square').addClass('fa-minus-square');
            $('#s1-area-adv-opts').show(1000);
            $('#' + archiveEngineInputId).val('manual').focus();
        };

        DUPX.onSafeModeSwitch = function ()
        {
            var safeObj = $('#' + exeSafeModeInputId)
            var mode = safeObj ? parseInt(safeObj.val()) : 0;
            var htWr = $('#' + htConfigWrapperId);
            var otWr = $('#' + otConfigWrapperId);

            switch (mode) {
                case 1:
                case 2:
                    htWr.find('#' + htConfigInputId + '_0').prop("checked", true);
                    htWr.find('input').prop("disabled", true);
                    otWr.find('#' + otConfigInputId + '_0').prop("checked", true);
                    otWr.find('input').prop("disabled", true);
                    break;
                case 0:
                default:
                    htWr.find('input').prop("disabled", false);
                    otWr.find('input').prop("disabled", false);
                    break;
            }
            console.log("mode set to" + mode);
        };

        /**
         * Open an in-line confirm dialog*/
        DUPX.confirmDeployment = function ()
        {

            if ($('#' + dbViewModeInputId).val() == 'cpnl') {
                dbhost = $("#" + cpnlDbHostInputId).val();
                dbname = $("#cpnl-dbname-result").val();
                dbuser = $("#cpnl-dbuser-result").val();
            } else {
<?php if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_DB_HOST)) { ?>
                    var dbhost = $("#" + dbHostInputId).val();
<?php } else { ?>
                    var dbhost = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_HOST)); ?>;
<?php } ?>
<?php if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_DB_NAME)) { ?>
                    var dbname = $("#" + dbNameInputId).val();
<?php } else { ?>
                    var dbname = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME)); ?>;
<?php } ?>
<?php if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_DB_USER)) { ?>
                    var dbuser = $("#" + dbUserInputId).val();
<?php } else { ?>
                    var dbuser = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_USER)); ?>;
<?php } ?>
            }

<?php if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_PATH_NEW)) { ?>
                var pathNew = $("#" + pathNewInputId).val();
<?php } else { ?>
                var pathNew = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW)); ?>;
<?php } ?>
<?php if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_URL_NEW)) { ?>
                var urlNew = $("#" + urlNewInputId).val();
<?php } else { ?>
                var urlNew = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW)); ?>;
<?php } ?>

            $('#dlg-path-new').html(pathNew);
            $('#dlg-url-new').html(urlNew);
            $('#dlg-dbhost').html(dbhost);
            $('#dlg-dbname').html(dbname);
            $('#dlg-dbuser').html(dbuser);

            $("#db-install-dialog-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 550,
                modal: true,
                position: {my: 'top', at: 'top+150'},
                buttons: {
                    "OkButton": {
                        text: "OK",
                        id: "db-install-dialog-confirm-button",
                        click: function () {
                            $(this).dialog("close");
                            DUPX.deployStep1();
                        }
                    },
                    "CancelButton": {
                        text: "Cancel",
                        id: "db-install-dialog-cancel-button",
                        click: function () {
                            $(this).dialog("close");
                        }
                    }
                }
            });
        };

        DUPX.toggleSetupType = function ()
        {
            var val = $("input:radio[name='setup_type']:checked").val();
            $('div.s1-setup-type-sub').hide();
            $('#s1-setup-type-sub-' + val).show(200);
        };

        /**
         * Accetps Usage Warning */
        DUPX.acceptWarning = function (agreeMsg)
        {
            if ($("#" + acceptContinueInputId).is(':checked')) {
                $("#s1-deploy-btn").removeAttr("disabled");
                $("#s1-deploy-btn").removeAttr("title");
            } else {
                $("#s1-deploy-btn").attr("disabled", "true");
                $("#s1-deploy-btn").attr("title", agreeMsg);
            }
        };

        DUPX.setPageActions = function (inputActions) {
            let actions = $.extend({}, {
                'error': false,
                'validate': false,
                'hwarn': false,
                'next': false
            }, inputActions);

            stepActions.addClass('no-display');
            if (actions.next) {
                stepActions.filter("#next_action").removeClass('no-display');
            }
            if (actions.validate) {
                stepActions.filter("#validate_action").removeClass('no-display');
            }
            if (actions.hwarn) {
                stepActions.filter("#hard_warning_action").removeClass('no-display');
            }
            if (actions.error) {
                stepActions.filter("#error_action").removeClass('no-display');
            }
        }

        DUPX.openValidateArea = function () {
            if (validateAreaHeader.hasClass('open')) {
                validateAreaHeader.trigger('click');
            }
        }

        DUPX.closeValidateArea = function () {
            if (validateAreaHeader.hasClass('close')) {
                validateAreaHeader.trigger('click');
            }
        }

        DUPX.openBasicSetupArea = function () {
            if (basicSetupAreaHeader.hasClass('open')) {
                basicSetupAreaHeader.trigger('click');
            }
        }

        DUPX.closeBasicSetupArea = function () {
            if (basicSetupAreaHeader.hasClass('close')) {
                basicSetupAreaHeader.trigger('click');
            }
        }

        DUPX.closeOptionsSetupArea = function () {
            if (optionsAreaHeader.hasClass('close')) {
                optionsAreaHeader.trigger('click');
            }
        }

        DUPX.resetValidationResult = function () {
            DUPX.setValidationBadge('#validate-global-badge-status', false);
            $('.database-setup-title').removeClass('warning');
            validateArea.find('#validation-result').empty().append(validateNoResult);
        }

        DUPX.autoUpdateOnMainChanges = function () {
            var originalUrlMainVal = $('#' + urlNewInputId).val();
            var urlRegex = new RegExp('^' + originalUrlMainVal, '');

            $('.auto-updatable').each(function () {
                $(this).data('original-default-value', $(this).find('input').val());
            });

            $('#' + urlNewInputId).bind("keyup change", function () {
                var newUrlVal = $(this).val().replace(/\/$/, '');
                $('.auto-updatable.autoupdate-enabled[data-auto-update-from-input="' + urlNewInputId + '"]').each(function () {
                    let originalVal = $(this).data('original-default-value');
                    $(this).find('input').val(originalVal.replace(urlRegex, newUrlVal));
                });
            });

            var orginalPathMainVal = $('#' + pathNewInputId).val();
            var pathRegex = new RegExp('^' + orginalPathMainVal, '');

            $('#' + pathNewInputId).bind("keyup change", function () {
                var newPathlVal = $(this).val().replace(/\/$/, '');
                $('.auto-updatable.autoupdate-enabled[data-auto-update-from-input="' + pathNewInputId + '"]').each(function () {
                    let originalVal = $(this).data('original-default-value');
                    $(this).find('input').val(originalVal.replace(pathRegex, newPathlVal));
                });
            });
        };

        DUPX.onValidateResult = function (validateData) {
            validateNoResult.detach();
            validateArea.find('#validation-result').empty().append(validateData.htmlResult);
            validateArea.find("*[data-type='toggle']").click(DUPX.toggleClick);
            DUPX.setValidationBadge('#validate-global-badge-status', validateData.mainBagedClass);

            DUPX.processValidationCharset(validateData.extraData);
            DUPX.processValidationDefaultCharset(validateData.extraData);

            if (validateData.categoriesLevels.database == 0) {
                $('.database-setup-title').addClass('warning');
                DUPX.openBasicSetupArea();
            } else {
                DUPX.closeBasicSetupArea();
            }
            DUPX.closeOptionsSetupArea();

            switch (validateData.mainLevel) {
                case <?php echo DUPX_Validation_abstract_item::LV_PASS; ?>:
                case <?php echo DUPX_Validation_abstract_item::LV_GOOD; ?>:
                    DUPX.openValidateArea();
                    DUPX.setPageActions({'next': true});
                    break;
                case <?php echo DUPX_Validation_abstract_item::LV_SOFT_WARNING; ?>:
                    DUPX.openValidateArea();
                    DUPX.setPageActions({'next': true});
                    break;
                case <?php echo DUPX_Validation_abstract_item::LV_HARD_WARNING; ?>:
                    DUPX.openValidateArea();
                    DUPX.setPageActions({'hwarn': true, 'next': true});
                    break;
                case <?php echo DUPX_Validation_abstract_item::LV_FAIL; ?>:
                default:
                    DUPX.openValidateArea();
                    DUPX.setPageActions({'error': true, 'validate': true});
            }
        };

        DUPX.reavelidateOnChangeAction = function (oldValue, obj) {
            if (obj.val() !== oldValue) {
                oldValue = obj.val();
                DUPX.resetValidationResult();
                DUPX.setPageActions({'validate': true});
            }
            return obj.val();
        }

        DUPX.revalidateOnChange = function () {
            $('.revalidate-on-change').each(function () {
                $(this).find('input, select, textarea').each(function () {
                    if ($(this).is(':checkbox, :radio')) {
                        $(this).bind("click", function () {
                            DUPX.reavelidateOnChangeAction(false, $(this));
                        });
                    } else {
                        var oldValue = $(this).val();
                        $(this).bind("keyup change", function () {
                            oldValue = DUPX.reavelidateOnChangeAction(oldValue, $(this));
                        });
                    }

                });
            });
        }

        //INIT Routines
        $("*[data-type='toggle']").click(DUPX.toggleClick);
        $(".tabs").tabs();

        DUPX.acceptWarning();
        DUPX.toggleSetupType();

        DUPX.autoUpdateOnMainChanges();
        DUPX.revalidateOnChange();

        validateArea.on("click", '#' + validationShowInputId, function () {
            if ($(this).is(":checked")) {
                validateArea.removeClass('show-warnings').addClass('show-all');
            } else {
                validateArea.removeClass('show-all').addClass('show-warnings');
            }
        });

        $('#s1-deploy-btn').click(function () {
            DUPX.confirmDeployment();
        });

        $('#validate-button').click(function () {
            DUPX.sendParamsStep1(step1Form, function () {
<?php
// reload page to reinit interface
$onValidatePrams = array(
    DUPX_Paramas_Manager::PARAM_CTRL_ACTION => 'ctrl-step1',
    DUPX_Security::CTRL_TOKEN               => DUPX_CSRF::generate('ctrl-step1'),
    DUPX_Paramas_Manager::PARAM_STEP_ACTION => DUPX_CTRL::ACTION_STEP_ON_VALIDATE
);
?>
                let onValidateParam = <?php echo DupProSnapJsonU::wp_json_encode($onValidatePrams); ?>;
                DUPX.redirect(DUPX.dupInstallerUrl, 'post', onValidateParam);
            });
        });

        $('.s1-switch-template-btn').click(function () {
            let tplButton = $(this);
            if (tplButton.hasClass('active') || !tplButton.data('template')) {
                return;
            }
<?php
$switchPrams     = array(
    DUPX_Paramas_Manager::PARAM_CTRL_ACTION => 'ctrl-step1',
    DUPX_Security::CTRL_TOKEN               => DUPX_CSRF::generate('ctrl-step1'),
    DUPX_Paramas_Manager::PARAM_STEP_ACTION => DUPX_CTRL::ACTION_STEP_SET_TEMPLATE,
);
?>
            let redirectParam = <?php echo DupProSnapJsonU::wp_json_encode($switchPrams); ?>;
            redirectParam[<?php echo DupProSnapJsonU::wp_json_encode(DUPX_Paramas_Manager::PARAM_TEMPLATE); ?>] = tplButton.data('template');
            DUPX.redirect(DUPX.dupInstallerUrl, 'post', redirectParam);
        });

        validateArea.on("click", ".test-title", function () {
            let content = $(this).closest('.test-wrapper').find('.test-content');
            let faIcon = $(this).find('> .fa');
            if (content.hasClass('no-display')) {
                faIcon.removeClass('fa-caret-right').addClass('fa-caret-down');
                content.removeClass('no-display');
            } else {
                faIcon.removeClass('fa-caret-down').addClass('fa-caret-right');
                content.addClass('no-display');
            }
        });

<?php
if (DUPX_Validation_manager::validateOnLoad()) {
    ?>
            DUPX.initialValidateAction(DUPX.onValidateResult, true, true);
<?php } ?>
    });
</script>
<?php
dupxTplRender('scripts/step1-deploy');
