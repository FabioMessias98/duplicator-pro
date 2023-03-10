<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();

if (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_OVR_INSTALL) {
    $overwriteData = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
    $ovr_dbhost    = $overwriteData['dbhost'];
    $ovr_dbname    = $overwriteData['dbname'];
    $ovr_dbuser    = $overwriteData['dbuser'];
    $ovr_dbpass    = $overwriteData['dbpass'];
} else {
    $ovr_dbhost = '';
    $ovr_dbname = '';
    $ovr_dbuser = '';
    $ovr_dbpass = '';
}
?>
<script>
    const dbViewModeInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE)); ?>;
    const dbHostInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_HOST)); ?>;
    const dbNameInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_NAME)); ?>;
    const dbUserInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_USER)); ?>;
    const dbPassInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_PASS)); ?>;
    const dbActionInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_ACTION)); ?>;
    const dbCharsetDefaultID = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_CHARSET)); ?>;
    const dbCollateDefaultID = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_COLLATE)); ?>;
    const dbDbcharsetfbInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB)); ?>;
    const dbDbcharsetfbValWrapperId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormWrapperId(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL)); ?>;
    const dbDbcharsetfbValInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL)); ?>;
    const dbDbcollatefbInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB)); ?>;
    const dbDbcollatefbValWrapperId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormWrapperId(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL)); ?>;
    const dbDbcollatefbValInputId = <?php echo DupProSnapJsonU::wp_json_encode($paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL)); ?>;

    DUPX.basicDBActionChange = function ()
    {
        var action = $('#' + dbActionInputId).val();
        $('.s2-basic-pane .s2-warning-manualdb').hide();
        $('.s2-basic-pane .s2-warning-emptydb').hide();
        $('.s2-basic-pane .s2-warning-renamedb').hide();
        switch (action)
        {
            case 'create'  :
                break;
            case 'empty'   :
                $('.s2-basic-pane .s2-warning-emptydb').show(300);
                break;
            case 'rename'  :
                $('.s2-basic-pane .s2-warning-renamedb').show(300);
                break;
            case 'manual'  :
                $('.s2-basic-pane .s2-warning-manualdb').show(300);
                break;
        }
    };

    DUPX.processValidationDefaultCharset = function (data)
    {
        if (!data.hasOwnProperty('charsetDefault')) {
            return false;
        }

        var charsetDefaultObj = $('#' + dbCharsetDefaultID);
        var collateDefaultObj = $('#' + dbCollateDefaultID);

        if (data.charsetDefault.defaultCharset !== undefined && data.charsetDefault.defaultCharset !== null) {
            charsetDefaultObj.val(data.charsetDefault.defaultCharset);
        }

        if (data.charsetDefault.defaultCollate !== undefined && data.charsetDefault.defaultCollate !== null) {
            collateDefaultObj.val(data.charsetDefault.defaultCollate);
        }

        return true;
    };

    DUPX.processValidationCharset = function (data)
    {
        var charsetValInputObj = $('#' + dbDbcharsetfbValInputId);
        var collateValInputObj = $('#' + dbDbcollatefbValInputId);
        charsetValInputObj.empty();
        collateValInputObj.empty();

        if (!data.hasOwnProperty('charsetTest')) {
            return false;
        }

        if (charsetValInputObj && typeof data.charsetTest.charset !== 'undefined') {
            for (i = 0; i < data.charsetTest.charset.list.length; i++) {
                let item = data.charsetTest.charset.list[i];

                $('<option>')
                        .attr('value', item)
                        .text(item)
                        .prop('selected', (data.charsetTest.charset.selected == item))
                        .appendTo(charsetValInputObj);
            }
        }

        if (collateValInputObj && typeof data.charsetTest.collate !== 'undefined') {
            for (i = 0; i < data.charsetTest.collate.list.length; i++) {
                let item = data.charsetTest.collate.list[i];

                var option = $('<option>')
                        .attr({
                            'value': item.Collation,
                            'data-charset': item.Charset
                        })
                        .text(item.Collation)
                        .prop('selected', (data.charsetTest.collate.selected == item.Collation))
                        .appendTo(collateValInputObj);

                if (typeof data.charsetTest.charset.selected == undefined || data.charsetTest.charset.selected == '') {
                } else if (data.charsetTest.charset.selected == item.Charset) {
                } else {
                    option.addClass('no-display');
                }
            }
        }

        $(charsetValInputObj).trigger('change');
        return true;
    }


    //DOCUMENT INIT
    $(document).ready(function ()
    {
        $("#" + dbActionInputId).on("change", DUPX.basicDBActionChange);
        DUPX.basicDBActionChange();

        DUPX.checkOverwriteParameters = function (dbhost, dbname, dbuser, dbpass)
        {
            $("#" + dbHostInputId).val(<?php echo DupProSnapJsonU::wp_json_encode($ovr_dbhost); ?>).prop('readonly', true);
            $("#" + dbNameInputId).val(<?php echo DupProSnapJsonU::wp_json_encode($ovr_dbname); ?>).prop('readonly', true);
            $("#" + dbUserInputId).val(<?php echo DupProSnapJsonU::wp_json_encode($ovr_dbuser); ?>).prop('readonly', true);
            $("#" + dbPassInputId).val(<?php echo DupProSnapJsonU::wp_json_encode($ovr_dbpass); ?>).prop('readonly', true);
            $("#s2-db-basic-setup").show();
        };

        DUPX.fillInPlaceHolders = function ()
        {
            $("#" + dbHostInputId).attr('placeholder', <?php echo DupProSnapJsonU::wp_json_encode($ovr_dbhost); ?>).prop('readonly', false);
            $("#" + dbNameInputId).attr('placeholder', <?php echo DupProSnapJsonU::wp_json_encode($ovr_dbname); ?>).prop('readonly', false);
            $("#" + dbUserInputId).attr('placeholder', <?php echo DupProSnapJsonU::wp_json_encode($ovr_dbuser); ?>).prop('readonly', false);
            $("#" + dbPassInputId).attr('placeholder', <?php echo DupProSnapJsonU::wp_json_encode($ovr_dbpass); ?>).prop('readonly', false);
        };

        DUPX.resetParameters = function ()
        {
            $("#" + dbHostInputId).val('').attr('placeholder', '').prop('readonly', false);
            $("#" + dbNameInputId).val('').attr('placeholder', '').prop('readonly', false);
            $("#" + dbUserInputId).val('').attr('placeholder', '').prop('readonly', false);
            $("#" + dbPassInputId).val('').attr('placeholder', '').prop('readonly', false);
        };

<?php if (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_OVR_INSTALL) : ?>
            DUPX.fillInPlaceHolders();
<?php endif; ?>
        DUPX.charsetfbCheckChanged = function () {
            var selectionBoxWrapper = $('#' + dbDbcharsetfbValWrapperId);
            if ($("#" + dbDbcharsetfbInputId).is(':checked')) {
                selectionBoxWrapper.slideDown('slow');
            } else {
                selectionBoxWrapper.slideUp('slow');
            }
        }

        DUPX.collatefbCheckChanged = function () {
            var selectionBoxWrapper = $('#' + dbDbcollatefbValWrapperId);
            if ($("#" + dbDbcollatefbInputId).is(':checked')) {
                selectionBoxWrapper.slideDown('slow');
            } else {
                selectionBoxWrapper.slideUp('slow');
            }
        }

        DUPX.charsetValChanged = function () {
            var collateObj = $('#' + dbDbcollatefbValInputId);
            var charsetObj = $('#' + dbDbcharsetfbValInputId);
            collateObj.find('option').hide();
            collateObj.find('option[data-charset="' + charsetObj.val() + '"]').show().first().prop('selected', true);
        }

        $("#" + dbDbcharsetfbInputId).change(DUPX.charsetfbCheckChanged).trigger('change');
        $("#" + dbDbcollatefbInputId).change(DUPX.collatefbCheckChanged).trigger('change');
        $("#" + dbDbcharsetfbValInputId).change(DUPX.charsetValChanged);


        $('#' + dbViewModeInputId).change(function () {
            switch ($(this).val()) {
                case 'cpnl':
                    $('.s2-cpnl-pane').removeClass('no-display');
                    $('.s2-basic-pane').addClass('no-display');
                    break;
                case 'basic':
                default:
                    $('.s2-cpnl-pane').addClass('no-display');
                    $('.s2-basic-pane').removeClass('no-display');
                    break;
            }
        });
    });
</script>
