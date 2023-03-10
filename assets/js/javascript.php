<?php defined("ABSPATH") or die(""); ?>
<?php ?>
<script>
    (function ($) {
        /* ============================================================================
         * DESCRIPTION: Methods and Objects in this file are global and common in
         * nature use this file to place all shared methods and varibles */

        //UNIQUE NAMESPACE
        DupPro = new Object();
        DupPro.UI = new Object();
        DupPro.Pack = new Object();
        DupPro.Tools = new Object();
        DupPro.Settings = new Object();
        DupPro.Storage = new Object();
        DupPro.Storage.Dropbox = new Object();
        DupPro.Storage.OneDrive = new Object();
        DupPro.Storage.FTP = new Object();
        DupPro.Storage.SFTP = new Object();
        DupPro.Storage.GDrive = new Object();
        DupPro.Storage.S3 = new Object();
        DupPro.Schedule = new Object();
        DupPro.Template = new Object();
        DupPro.Support = new Object();
        DupPro.Debug = new Object();

        //GLOBAL CONSTANTS
        DupPro.DEBUG_AJAX_RESPONSE = false;
        DupPro.AJAX_TIMER = null;

        DupPro._WordPressInitDateTime = '<?php echo current_time("D M d Y H:i:s O") ?>';
        DupPro._WordPressInitTime = '<?php echo current_time("H:i:s") ?>';
        DupPro._ServerInitDateTime = '<?php echo date("D M d Y H:i:s O") ?>';
        DupPro._ClientInitDateTime = new Date();

        DupPro.parseJSON = function (mixData) {
            try {
                var parsed = JSON.parse(mixData);
                return parsed;
            } catch (e) {
                console.log("JSON parse failed - 1");
                console.log(mixData);
            }

            if (mixData.indexOf('[') > -1 && mixData.indexOf('{') > -1) {
                if (mixData.indexOf('{') < mixData.indexOf('[')) {
                    var startBracket = '{';
                    var endBracket = '}';
                } else {
                    var startBracket = '[';
                    var endBracket = ']';
                }
            } else if (mixData.indexOf('[') > -1 && mixData.indexOf('{') === -1) {
                var startBracket = '[';
                var endBracket = ']';
            } else {
                var startBracket = '{';
                var endBracket = '}';
            }

            var jsonStartPos = mixData.indexOf(startBracket);
            var jsonLastPos = mixData.lastIndexOf(endBracket);
            if (jsonStartPos > -1 && jsonLastPos > -1) {
                var expectedJsonStr = mixData.slice(jsonStartPos, jsonLastPos + 1);
                try {
                    var parsed = JSON.parse(expectedJsonStr);
                    return parsed;
                } catch (e) {
                    console.log("JSON parse failed - 2");
                    console.log(mixData);
                    throw e;
                    // errorCallback(xHr, textstatus, 'extract');
                    return false;
                }
            }
            // errorCallback(xHr, textstatus, 'extract');
            throw "could not parse the JSON";
            return false;
        }

        /* ============================================================================
         *  BASE NAMESPACE: All methods at the top of the Duplicator Namespace
         * ============================================================================ */

        /* Starts a timer for Ajax calls */
        DupPro.StartAjaxTimer = function ()
        {
            DupPro.AJAX_TIMER = new Date();
        };

        /*	Ends a timer for Ajax calls */
        DupPro.EndAjaxTimer = function ()
        {
            var endTime = new Date();
            DupPro.AJAX_TIMER = (endTime.getTime() - DupPro.AJAX_TIMER) / 1000;
        };

        /**
         *
         * @param string message // html message conent
         * @param string errLevel // notice warning error
         * @param function updateCallback // called after message content is updated
         * 
         * @returns void
         */
        DupPro.addAdminMessage = function (message, errLevel, options) {
            let settings = $.extend({}, {
                'isDismissible': true,
                'hideDelay': 0, // 0 no hide or millisec
                'updateCallback': false
            }, options);

            var classErrLevel = 'notice';
            switch (errLevel) {
                case 'error':
                    classErrLevel = 'error';
                    break;
                case 'warning':
                    classErrLevel = 'update-nag';
                    break;
                case 'notice':
                default:
                    classErrLevel = 'updated';
                    break;

            }

            var noticeCLasses = 'notice ' + classErrLevel + ' no_display';
            if (settings.isDismissible) {
                noticeCLasses += ' is-dismissible';
            }

            var msgNode = $('<div class="' + noticeCLasses + '">' +
                    '<div class="margin-top-1 margin-bottom-1 msg-content">' + message + '</div>' +
                    '</div>');
            var dismissButton = $('<button type="button" class="notice-dismiss">' +
                    '<span class="screen-reader-text">Dismiss this notice.</span>' +
                    '</button>');

            var anchor = $("#wpcontent");
            if (anchor.find('.wrap').length) {
                anchor = anchor.find('.wrap').first();
            }

            if (anchor.find('h1').length) {
                anchor = anchor.find('h1').first();
                msgNode.insertAfter(anchor);
            } else {
                msgNode.prependTo(anchor);
            }

            if (settings.isDismissible) {
                dismissButton.appendTo(msgNode).click(function () {
                    dismissButton.closest('.is-dismissible').fadeOut("slow", function () {
                        $(this).remove();
                    });
                });
            }

            if (typeof settings.updateCallback === "function") {
                settings.updateCallback(msgNode);
            }

            $("body, html").animate({scrollTop: 0}, 500);
            $(msgNode).css('display', 'none').removeClass("no_display").fadeIn("slow", function () {
                if (settings.hideDelay > 0) {
                    setTimeout(function () {
                        dismissButton.closest('.is-dismissible').fadeOut("slow", function () {
                            $(this).remove();
                        });
                    }, settings.hideDelay);
                }
            });
        };

        /**
         * 
         * @param string filename
         * @param string content
         * @param string mimeType // text/html, text/plain
         * @returns {undefined}
         */
        DupPro.downloadContentAsfile = function (filename, content, mimeType) {
            mimeType = (typeof mimeType !== 'undefined') ? mimeType : 'text/plain';
            var element = document.createElement('a');
            element.setAttribute('href', 'data:' + mimeType + ';charset=utf-8,' + encodeURIComponent(content));
            element.setAttribute('download', filename);

            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        }

        DupPro.ajaxLoaderObj = null;

        DupPro.ajaxLoaderShow = function () {
            if (DupPro.ajaxLoaderObj === null) {
                DupPro.ajaxLoaderObj = $('#dup-pro-ajax-loader')
            }
            DupPro.ajaxLoaderObj.stop().css('display', 'block').animate({
                opacity: 1
            }, 200);
        }

        DupPro.ajaxLoaderHide = function () {
            DupPro.ajaxLoaderObj.stop().css({
                'display': 'none',
                'opacity': 0
            });
        }

        DupPro.StandarJsonAjaxWrapper = function (ajaxData, callbackSuccess, callbackFail) {
            DupPro.ajaxLoaderShow();

            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: ajaxData,
                success: function (result, textStatus, jqXHR) {
                    var message = '';
                    if (result.success) {
                        if (typeof callbackSuccess === "function") {
                            try {
                                message = callbackSuccess(result.data.funcData, result.data, textStatus, jqXHR);
                            } catch (error) {
                                console.error(error);
                                DupPro.addAdminMessage(error.message, 'error');
                                message = '';
                            }
                        } else {
                            message = '<?php DUP_PRO_U::_e('RESPONSE SUCCESS'); ?>';
                        }

                        if (String(message).length) {
                            DupPro.addAdminMessage(message, 'notice');
                        }
                    } else {
                        if (typeof callbackFail === "function") {
                            try {
                                message = callbackFail(result.data.funcData, result.data, textStatus, jqXHR);
                            } catch (error) {
                                console.error(error);
                                message = error.message;
                            }
                        } else {
                            message = '<?php DUP_PRO_U::_e('RESPONSE ERROR!'); ?>' + '<br><br>' + result.data.message;
                        }
                        if (String(message).length) {
                            DupPro.addAdminMessage(message, 'error');
                        }
                    }
                },
                error: function (result) {
                    DupPro.addAdminMessage(<?php echo DupProSnapJsonU::wp_json_encode(DUP_PRO_U::__('AJAX ERROR!').'<br>'.DUP_PRO_U::__('Ajax request error')); ?>, 'error');
                },
                complete: function () {
                    DupPro.ajaxLoaderHide();
                }
            });
        };

        /*	Reloads the current window
         *	@param data		An xhr object  */
        DupPro.ReloadWindow = function (data, queryString)
        {
            if (DupPro.DEBUG_AJAX_RESPONSE) {
                DupPro.Pack.ShowError('debug on', data);
            } else {
                var url = window.location.href;
                if (typeof queryString !== 'undefined') {
                    var character = '?';
                    if (url.indexOf('?') > -1) {
                        character = '&';
                    }
                    url += character + queryString;
                }
                window.location = url;
            }
        };

        /* Basic Util Methods here */
        DupPro.OpenLogWindow = function (log)
        {
            var logFile = log || null;
            if (logFile == null) {
                window.open('?page=duplicator-pro-tools', 'Log Window');
            } else {
                window.open('<?php echo DUPLICATOR_PRO_SSDIR_URL; ?>' + '/' + log)
            }
        };

        DupPro.humanFileSize = function (size)
        {
            var i = Math.floor(Math.log(size) / Math.log(1024));
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
        };


        /* ============================================================================
         *  UI NAMESPACE: All methods at the top of the Duplicator Namespace
         *  =========================================================================== */

        /*  Stores the state of a view into the database  */
        DupPro.UI.SaveViewStateByPost = function (key, value)
        {
            if (key != undefined && value != undefined) {
                jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    dataType: "json",
                    data: {action: 'DUP_PRO_UI_ViewState_SaveByPost', key: key, value: value, nonce: '<?php echo wp_create_nonce('DUP_PRO_UI_ViewState_SaveByPost'); ?>'},
                    success: function (data) {},
                    error: function (data) {}
                });
            }
        }

        DupPro.UI.SaveMulViewStatesByPost = function (states)
        {
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                dataType: "json",
                data: {action: 'DUP_PRO_UI_ViewState_SaveByPost', states: states, nonce: '<?php echo wp_create_nonce('DUP_PRO_UI_ViewState_SaveByPost'); ?>'},
                success: function (data) {},
                error: function (data) {}
            });
        }

        DupPro.UI.SetScanMode = function ()
        {
            var scanMode = jQuery('#scan-mode').val();

            if (scanMode == <?php echo DUP_PRO_PHPDump_Mode::Multithreaded ?>) {
                jQuery('#scan-multithread-size').show();
                jQuery('#scan-chunk-size-label').show();
            } else {
                jQuery('#scan-multithread-size').hide();
                jQuery('#scan-chunk-size-label').hide();
            }

        }

        DupPro.UI.IsSaveViewState = true;
        /*	Toggle MetaBoxes */
        DupPro.UI.ToggleMetaBox = function ()
        {
            var $title = jQuery(this);
            var $panel = $title.parent().find('.dup-box-panel');
            var $arrow = $title.parent().find('.dup-box-arrow i');
            var key = $panel.attr('id');
            var value = $panel.is(":visible") ? 0 : 1;
            $panel.toggle();
            if (DupPro.UI.IsSaveViewState)
                DupPro.UI.SaveViewStateByPost(key, value);
            (value)
                    ? $arrow.removeClass().addClass('fa fa-caret-up')
                    : $arrow.removeClass().addClass('fa fa-caret-down');

        }

        DupPro.UI.ClearTraceLog = function (reload)
        {
            var reload = reload || 0;
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: 'duplicator_pro_delete_trace_log',
                    nonce: '<?php echo wp_create_nonce('duplicator_pro_delete_trace_log'); ?>'
                },
                success: function (respData) {
                    try {
                        var data = DupPro.parseJSON(respData);
                    } catch (err) {
                        console.error(err);
                        console.error('JSON parse failed for response data: ' + respData);
                        return false;
                    }
                    if (reload) {
                        window.location.reload();
                    }
                },
                error: function (data) {}
            });
            return false;
        }

        /*	Toggle Password input */
        DupPro.UI.TogglePasswordDisplay = function (display, inputID)
        {
            if (display) {
                document.getElementById(inputID).type = "text";
            } else {
                document.getElementById(inputID).type = "password";
            }
        }

        /* Clock generator, used to show an active clock.
         * Intended use is to be called once per page load
         * such as:
         *		<div id="dpro-clock-container"></div>
         *		DupPro.UI.Clock(DupPro._WordPressInitTime); */
        DupPro.UI.Clock = function ()
        {
            var timeDiff;
            var timeout;

            function addZ(n) {
                return (n < 10 ? '0' : '') + n;
            }

            function formatTime(d) {
                return addZ(d.getHours()) + ':' + addZ(d.getMinutes()) + ':' + addZ(d.getSeconds());
            }

            return function (s) {

                var now = new Date();
                var then;
                // Set lag to just after next full second
                var lag = 1015 - now.getMilliseconds();

                // Get the time difference when first run
                if (s) {
                    s = s.split(':');
                    then = new Date(now);
                    then.setHours(+s[0], +s[1], +s[2], 0);
                    timeDiff = now - then;
                }

                now = new Date(now - timeDiff);
                jQuery('#dpro-clock-container').html(formatTime(now));
                timeout = setTimeout(DupPro.UI.Clock, lag);
            };
        }();

        /* ============================================================================
         *  Util functions
         *  =========================================================================== */
        DupPro.Util = {};

        DupPro.Util.isEmpty = function (val) {
            return (val === undefined || val == null || val.length <= 0) ? true : false;
        };

        jQuery(document).ready(function ($)
        {

            DupPro.UI.loadQtip = function ()
            {
                $('[data-tooltip!=""]').each(function () {
                    var element = $(this);
                    if (element.data('dup-pro-qtip-inizialized') == true) {
                        return;
                    }

                    let contentVals = {
                        attr: 'data-tooltip'
                    };
                    if ($(this)[0].hasAttribute("data-tooltip-title")) {
                        contentVals.title = $(this).data('tooltip-title');
                    }

                    element.qtip({
                        content: contentVals,
                        style: {
                            classes: 'qtip-dup-pro-layout',
                            width: 500,
                            zIndex: 1000000
                        },
                        position: {
                            my: 'top left',
                            at: 'bottom center'
                        }
                    }).data('dup-pro-qtip-inizialized', true);
                });

                $('[data-dup-pro-copy-value]').each(function () {
                    var element = $(this);
                    if (element.hasClass('disabled')) {
                        return;
                    }

                    if (element.data('dup-pro-copy-value-inizialized') == true) {
                        return;
                    }

                    var copyFailed = 'unable to copy';
                    var copyTitle = element.is('[data-dup-pro-copy-title]') ? element.data('dup-pro-copy-title') : 'Copy to clipboard';

                    element.click(function () {

                        if (element.hasClass('disabled')) {
                            return;
                        }

                        var valueToCopy = element.data('dup-pro-copy-value');
                        var copiedTitle = element.is('[data-dup-pro-copied-title]') ? element.data('dup-pro-copied-title') : valueToCopy + ' copied to clipboard';
                        var message = '';

                        var tmpArea = jQuery("<textarea></textarea>").css({
                            position: 'absolute',
                            top: '-10000px'
                        }).text(valueToCopy).appendTo("body");
                        tmpArea.select();
                        try {
                            var successful = document.execCommand('copy');
                            message = successful ? copiedTitle : copyFailed;
                        } catch (err) {
                            message = copyFailed;
                        }

                        element.qtip('option', 'style.classes', 'qtip-top-zindex qtip-dup-pro-layout light-colors');
                        element.qtip('option', 'content.text', message).addClass('light-colors').qtip('show');
                        setTimeout(function () {
                            element.qtip('option', 'style.classes', 'qtip-top-zindex qtip-dup-pro-layout');
                            element.qtip('option', 'content.text', copyTitle).removeClass('light-colors');
                        }, 2000);
                    }).qtip({
                        content: {
                            text: copyTitle
                        },
                        style: {
                            classes: 'qtip-top-zindex qtip-dup-pro-layout'
                        },
                        position: {
                            my: 'top left',
                            at: 'bottom center'
                        }
                    }).data('dup-pro-copy-value-text-inizialized', true);
                });
            }

            //INIT: DupPro Tabs
            $("div[data-dpro-tabs='true']").each(function ()
            {
                //Load Tab Setup
                var $root = $(this);
                var $lblRoot = $root.find('ul:first-child')
                var $lblKids = $lblRoot.children('li');
                var $lblKidsA = $lblRoot.children('li a');
                var $pnls = $root.children('div');

                //Apply Styles
                $root.addClass('categorydiv');
                $lblRoot.addClass('category-tabs');
                $pnls.addClass('tabs-panel').css('display', 'none');
                $lblKids.eq(0).addClass('tabs').css('font-weight', 'bold');
                $pnls.eq(0).show();

                var _clickEvt = function (evt)
                {
                    var $target = $(evt.target);
                    if (evt.target.nodeName == 'A') {
                        var $target = $(evt.target).parent();
                    }
                    var $lbls = $target.parent().children('li');
                    var $pnls = $target.parent().parent().children('div');
                    var index = $target.index();

                    $lbls.removeClass('tabs').css('font-weight', 'normal');
                    $lbls.eq(index).addClass('tabs').css('font-weight', 'bold');
                    $pnls.hide();
                    $pnls.eq(index).show();
                }

                //Attach Events
                $lblKids.click(_clickEvt);
                $lblKids.click(_clickEvt);
            });

            //INIT: Toggle MetaBoxes
            $('div.dup-box div.dup-box-title').each(function () {
                var $title = $(this);
                var $panel = $title.parent().find('.dup-box-panel');
                var $arrow = $title.find('.dup-box-arrow');
                $title.click(DupPro.UI.ToggleMetaBox);
                ($panel.is(":visible"))
                        ? $arrow.html('<i class="fa fa-caret-up"></i>')
                        : $arrow.html('<i class="fa fa-caret-down"></i>');
            });

            DupPro.UI.loadQtip();

            //HANDLEBARS HELPERS
            if (typeof (Handlebars) != "undefined") {

                function _handleBarscheckCondition(v1, operator, v2) {
                    switch (operator) {
                        case '==':
                            return (v1 == v2);
                        case '===':
                            return (v1 === v2);
                        case '!==':
                            return (v1 !== v2);
                        case '<':
                            return (v1 < v2);
                        case '<=':
                            return (v1 <= v2);
                        case '>':
                            return (v1 > v2);
                        case '>=':
                            return (v1 >= v2);
                        case '&&':
                            return (v1 && v2);
                        case '||':
                            return (v1 || v2);
                        case 'obj||':
                            v1 = typeof (v1) == 'object' ? v1.length : v1;
                            v2 = typeof (v2) == 'object' ? v2.length : v2;
                            return (v1 != 0 || v2 != 0);
                        default:
                            return false;
                    }
                }

                Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
                    return _handleBarscheckCondition(v1, operator, v2)
                            ? options.fn(this)
                            : options.inverse(this);
                });

                Handlebars.registerHelper('if_eq', function (a, b, opts) {
                    return (a == b) ? opts.fn(this) : opts.inverse(this);
                });
                Handlebars.registerHelper('if_neq', function (a, b, opts) {
                    return (a != b) ? opts.fn(this) : opts.inverse(this);
                });
            }

            //Prevent notice boxes from flashing as its re-positioned in DOM
            $('div.dpro-wpnotice-box').show(300);

        });
    })(jQuery);
</script>
