<?php
/** ================================================================================
  Plugin Name: Duplicator Pro
  Plugin URI: http://snapcreek.com/
  Description: Create, schedule and transfer a copy of your WordPress files and database. Duplicate and move a site from one location to another quickly.
  Version: 4.0.0.1
  Requires at least: 4.0
  Tested up to: 5.4.1
  Requires PHP: 5.3.8
  Author: Snap Creek
  Author URI: http://snapcreek.com
  Network: true
  License: GPLv2 or later

  Copyright 2011-2017  Snapcreek LLC

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  ================================================================================ */
defined('ABSPATH') || exit;

require_once(dirname(__FILE__)."/tools/php.version.check.php");
if (duplicator_pro_check_php_version() == false) {
    return;
}

require_once(dirname(__FILE__).'/lib/snaplib/snaplib.all.php');
require_once(dirname(__FILE__)."/helper.php");
require_once(dirname(__FILE__)."/define.php");

require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.string.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.date.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.zip.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.license.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.upgrade.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.validator.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.tree.files.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/utilities/class.u.wp.php');

require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.plugin.upgrade.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.crypt.blowfish.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.system.global.entity.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.profilelogs.entity.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/class.pack.runner.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.constants.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.db.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/ui/class.ui.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/ui/class.ui.alert.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.logging.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.restoreonly.package.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/host/class.custom.host.manager.php');

DUP_PRO_U::init();
DUP_PRO_DB::init();
DUP_PRO_Constants::init();

$dpro_license_key = get_option(DUP_PRO_Constants::LICENSE_KEY_OPTION_NAME, '');
if (!empty($dpro_license_key)) {

    $global = DUP_PRO_Global_Entity::get_instance();

    // RSR TODO: only init this if not an override key and we are active
    if (($global !== null) &&
        (!DUP_PRO_License_U::isValidOvrKey($dpro_license_key)) &&
        ($global->license_status !== DUP_PRO_License_Status::Invalid) &&
        ($global->license_status !== DUP_PRO_License_Status::Unknown)) {

        if (!class_exists('EDD_SL_Plugin_Updater')) {
            require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/lib/edd/EDD_SL_Plugin_Updater.php');
        }

        // Don't bother checking updates if license key isn't filled in since that will just create unnecessary traffic
        $dpro_edd_opts = array('version'     => DUPLICATOR_PRO_VERSION,
            'license'     => $dpro_license_key,
            'item_name'   => EDD_DUPPRO_ITEM_NAME,
            'author'      => 'Snap Creek Software',
            'cache_time'  => DUP_PRO_Constants::EDD_API_CACHE_TIME,
            'wp_override' => true);

        $edd_updater = new EDD_SL_Plugin_Updater(EDD_DUPPRO_STORE_URL, __FILE__, $dpro_edd_opts, DUP_PRO_Constants::PLUGIN_SLUG);

        if (!empty($_REQUEST['dup_pro_clear_updater_cache'])) {
            $edd_updater->clear_version_cache();
        }
    }
}

if (is_admin() || get_transient(DUPLICATOR_PRO_FRONTEND_TRANSITIENT) === false) {
    if (!is_admin()) {
        set_transient(DUPLICATOR_PRO_FRONTEND_TRANSITIENT, true, DUPLICATOR_PRO_FRONTEND_ACTION_DELAY);
    }

    // Only start the package runner and tracing once it's been confirmed that everything has been installed
    if (get_option(DUP_PRO_Plugin_Upgrade::DUP_VERSION_OPT_KEY) == DUPLICATOR_PRO_VERSION) {
        DUP_PRO_Package_Runner::init();

        $dpro_global_obj = DUP_PRO_Global_Entity::get_instance();

        // Important - Needs to be outside of is_admin for proper measuring of background processes
        if (($dpro_global_obj !== null) && ($dpro_global_obj->trace_profiler_on)) {
            $profileLogsEntity = DUP_PRO_Profile_Logs_Entity::get_instance();
            if ($profileLogsEntity != null) {
                DUP_PRO_LOG::setProfileLogs($profileLogsEntity->profileLogs);
                DUP_PRO_LOG::trace("set profile logs");
            }
        }
    }
}

if (is_admin()) {
    if (!empty($_REQUEST['dup_pro_clear_schedule_failure'])) {
        $system_global                  = DUP_PRO_System_Global_Entity::get_instance();
        $system_global->schedule_failed = false;
        $system_global->save();
    }

    if (!defined('WP_MAX_MEMORY_LIMIT')) {
        define('WP_MAX_MEMORY_LIMIT', '256M');
    }

    if (DupProSnapLibUtil::wp_is_ini_value_changeable('memory_limit')) {
        @ini_set('memory_limit', WP_MAX_MEMORY_LIMIT);
    }

    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.global.entity.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.package.template.entity.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/ui/class.ui.viewstate.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/ui/class.ui.notice.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/ui/class.ui.messages.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.server.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/package/class.pack.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.json.entity.base.php');
    require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/views/packages/screen.php');

    //Controllers
    require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/ctrls/class.web.services.php');
    require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/ctrls/ctrl.schedule.php');
    require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/ctrls/ctrl.package.php');
    require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/ctrls/ctrl.tools.php');
    require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/ctrls/ctrl.import.php');
    require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/ctrls/ctrl.import.installer.php');
    require_once (DUPLICATOR_PRO_PLUGIN_PATH.'/ctrls/ctrl.recovery.php');


    /** ========================================================
     * ACTIVATE/DEACTIVE/UPDATE HOOKS
     * =====================================================  */
    register_activation_hook(__FILE__, array('DUP_PRO_Plugin_Upgrade', 'onActivationAction'));
    register_deactivation_hook(__FILE__, 'duplicator_pro_deactivate');

    /**
     * Plugins Loaded:
     * Hooked into `plugin_loaded`.  Called once any activated plugins have been loaded.
     *
     * @access global
     * @return null
     */
    function duplicator_pro_plugins_loaded()
    {
        if (DUPLICATOR_PRO_VERSION != get_option(DUP_PRO_Plugin_Upgrade::DUP_VERSION_OPT_KEY)) {
            DUP_PRO_Plugin_Upgrade::onActivationAction();
        }
        load_plugin_textdomain(DUP_PRO_Constants::PLUGIN_SLUG, FALSE, dirname(plugin_basename(__FILE__)).'/lang/');

        try {
            duplicator_pro_patched_data_initialization();
        }
        catch (Exception $ex) {
            DUP_PRO_LOG::traceError("Could not do data initialization. ".$ex->getMessage());
        }
    }

    /**
     * Data Patches:
     * Handles data that needs to be initialized because of fixes etc
     *
     * @access global
     * @return null
     */
    function duplicator_pro_patched_data_initialization()
    {
        $global = DUP_PRO_Global_Entity::get_instance();
        if (is_null($global)) {
            DUP_PRO_Plugin_Upgrade::onActivationAction();
            $global = DUP_PRO_Global_Entity::get_instance();
        } else {
            $global->configure_dropbox_transfer_mode();

            if ($global->initial_activation_timestamp == 0) {
                $global->initial_activation_timestamp = time();
                $global->save();
            }
        }
    }

    /**
     * Deactivation Hook:
     * Hooked into `register_deactivation_hook`.  Routines used to deactivate the plugin
     * For uninstall see uninstall.php  WordPress by default will call the uninstall.php file
     *
     * @access global
     * @return null
     */
    function duplicator_pro_deactivate()
    {
        //Logic has been added to uninstall.php
        //Force recalculation of next run time on activation
        //see the function DUP_PRO_Package_Runner::calculate_earliest_schedule_run_time()
        DUP_PRO_Log::trace("Resetting next run time for active schedules");
        $activeSchedules = DUP_PRO_Schedule_Entity::get_active();
        foreach ($activeSchedules as $activeSchedule) {
            $activeSchedule->next_run_time = -1;
            $activeSchedule->save();
        }
    }

    /**
     * Footer Hook:
     * Hooked into `admin_footer`.  Returns display elements for the admin footer area
     *
     * @access global
     * @return string A footer element for downloading a link
     */
    function duplicator_pro_admin_footer()
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        $trace_on     = get_option('duplicator_pro_trace_log_enabled', false);
        $txt_trace_on = DUP_PRO_U::__("Turn Off");
        $profiling_on = $global->trace_profiler_on;

        if ($profiling_on) {
            $txt_trace_on .= ' '.DUP_PRO_U::__('(P)');
        }

        $txt_trace_title = DUP_PRO_U::__('TRACE LOG OPTIONS');
        $txt_trace_read  = DUP_PRO_U::__('View');
        $txt_trace_load  = DUP_PRO_U::__("Download").' ('.DUP_PRO_LOG::getTraceStatus().')';
        $txt_trace_zero  = DUP_PRO_U::__("Download").' (0B)';
        $txt_clear_trace = DUP_PRO_U::__('Clear');
        $url             = wp_nonce_url('admin.php?page=duplicator-pro-settings&_logging_mode=off&action=trace', 'duppro-settings-general-edit', '_wpnonce');
        $nonce           = wp_create_nonce('duplicator_pro_get_trace_log');

        // ?page=duplicator-pro-tools&tab=diagnostics&section=log
        /*
          Array
          (
          [page] => duplicator-pro-tools
          [tab] => diagnostics
          [section] => log
          )
         */
        if (isset($_GET['page']) && 'duplicator-pro-tools' == $_GET['page'] && isset($_GET['tab']) && ('diagnostics' == $_GET['tab'] || 'd' == $_GET['tab']) && isset($_GET['section']) && 'log' == $_GET['section']) {
            $clear_trace_log_js = 'DupPro.UI.ClearTraceLog(1);';
        } else {
            $clear_trace_log_js = 'DupPro.UI.ClearTraceLog(0); jQuery("#dup_pro_trace_txt").html("'.$txt_trace_zero.'"); ';
        }

        $html = <<<HTML
			<style>p#footer-upgrade {display:none}</style>
			<div id='dpro-monitor-trace-area'>
				<b>{$txt_trace_title}</b><br/>
				<a class='button button-small' href="admin.php?page=duplicator-pro-tools&tab=diagnostics&section=log" target="_duptracelog"><i class="fa fa-file-alt"></i> {$txt_trace_read}</a>
                <a class='button button-small' onclick='{$clear_trace_log_js}'><i class="fa fa-times"></i> {$txt_clear_trace}</a>
				<a class='button button-small' onclick="var actionLocation = ajaxurl + '?action=duplicator_pro_get_trace_log&nonce={$nonce}'; location.href = actionLocation;"><i class="fa fa-download"></i> <span id='dup_pro_trace_txt'>{$txt_trace_load}</span></a>
				<a class='button button-small' href='{$url}' onclick='window.location.reload();'><i class="fa fa-power-off"></i> {$txt_trace_on}</a>
			</div>
HTML;
        if ($trace_on)
            echo $html;
    }
    /** ========================================================
     * ACTION HOOKS
     * =====================================================  */
    $web_services                           = new DUP_PRO_Web_Services();
    $web_services->init();
    $GLOBALS['CTRLS_DUP_PRO_CTRL_Tools']    = new DUP_PRO_CTRL_Tools();
    $GLOBALS['CTRLS_DUP_PRO_CTRL_Package']  = new DUP_PRO_CTRL_Package();
    $GLOBALS['CTRLS_DUP_PRO_CTRL_Schedule'] = new DUP_PRO_CTRL_Schedule();

    add_action('plugins_loaded', 'duplicator_pro_plugins_loaded');
    add_action('plugins_loaded', 'duplicator_pro_wpfront_integrate');
    add_action('admin_init', 'duplicator_pro_init');

    if (isset($_REQUEST['page']) && DUP_PRO_STR::contains($_REQUEST['page'], 'duplicator-pro')) {
        add_action('admin_footer', 'duplicator_pro_admin_footer');
    }

    add_action('wp_ajax_DUP_PRO_UI_ViewState_SaveByPost', array('DUP_PRO_UI_ViewState', 'saveByPost'));

    if (is_multisite()) {
        add_action('network_admin_menu', 'duplicator_pro_menu');
        add_action('network_admin_notices', array('DUP_PRO_UI_Alert', 'licenseAlertCheck'));
        add_action('network_admin_notices', array('DUP_PRO_UI_Alert', 'activatePluginsAfterInstall'));
    } else {
        add_action('admin_menu', 'duplicator_pro_menu');
        add_action('admin_notices', array('DUP_PRO_UI_Alert', 'licenseAlertCheck'));
        add_action('admin_notices', array('DUP_PRO_UI_Alert', 'failedScheduleCheck'));
        add_action('admin_notices', array('DUP_PRO_UI_Alert', 'activatePluginsAfterInstall'));
    }

    add_action('admin_enqueue_scripts', 'duplicator_pro_admin_enqueue_scripts');

    DUP_PRO_UI_Notice::init();

    /**
     * Action Hook:
     * User role editor integration
     *
     * @access global
     * @return null
     */
    function duplicator_pro_wpfront_integrate()
    {
        $global = DUP_PRO_Global_Entity::get_instance();

        if ($global->wpfront_integrate) {
            do_action('wpfront_user_role_editor_duplicator_pro_init', array('export', 'manage_options', 'read'));
        }
    }

    /**
     * Action Hook:
     * Hooked into `admin_init`.  Init routines for all admin pages
     *
     * @access global
     * @return null
     */
    function duplicator_pro_init()
    {
        DUP_PRO_RestoreOnly_Package::getInstance()->init();
        // custom host init
        DUP_PRO_Custom_Host_Manager::getInstance()->init();
        $global = DUP_PRO_Global_Entity::get_instance();
        if (!($global instanceof DUP_PRO_Global_Entity)) {
            if (is_admin()) {
                add_action('admin_notices', array('DUP_PRO_UI_Alert', 'showTablesCorrupted'));
                add_action('network_admin_notices', array('DUP_PRO_UI_Alert', 'showTablesCorrupted'));
            }
            return;
        }
        // Check post migration hook and take action of post migration
        $is_migration = get_option('duplicator_pro_migration');
        if ($is_migration) {
            $global->lock_mode                 = DUP_PRO_Global_Entity::get_lock_type();
            $global->ajax_protocol             = DUP_PRO_Global_Entity::get_ajax_protocol();
            $global->server_kick_off_sslverify = DUP_PRO_Global_Entity::get_server_kick_sslverify_flag();
            if ($global->archive_build_mode !== DUP_PRO_Archive_Build_Mode::DupArchive) {
                $global->set_build_mode();
            }
            $global->save();
            flush_rewrite_rules(true);

            // remove point in database but not tje files.
            DUP_PRO_Package_Recover::resetRecoverPackage();

            delete_option('duplicator_pro_migration');
        }

        $duplicator_pro_reset_user_settings_required = get_option('duplicator_pro_reset_user_settings_required', 0);
        if ($duplicator_pro_reset_user_settings_required) {
            $global->ResetUserSettings();
            $global->save();
            update_option('duplicator_pro_reset_user_settings_required', 0);
        }

        DUP_PRO_CTRL_import::init();
        DUP_PRO_CTRL_recovery::init();

        // wp_doing_ajax introduced in WP 4.7
        if (!function_exists('wp_doing_ajax') || (!wp_doing_ajax() )) {
            // CSS
            wp_register_style('dup-pro-jquery-ui', DUPLICATOR_PRO_PLUGIN_URL.'assets/css/jquery-ui.css', null, "1.11.2");
            wp_register_style('dup-pro-font-awesome', DUPLICATOR_PRO_PLUGIN_URL.'assets/css/fontawesome-all.min.css', null, '5.7.2');
            wp_register_style('parsley', DUPLICATOR_PRO_PLUGIN_URL.'assets/css/parsley.css', null, '2.0.6');
            wp_register_style('jquery-qtip', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/jquery.qtip/jquery.qtip.min.css', null, '3.0.3');
            wp_register_style('formstone', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/formstone/bundle.css', null, 'v1.4.16-1');
            wp_register_style('jstree', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/jstree/themes/snap/style.css', null, '3.8.1');
            wp_register_style('dup-pro-plugin-style', DUPLICATOR_PRO_PLUGIN_URL.'assets/css/style.css', array(
                'dup-pro-jquery-ui',
                'dup-pro-font-awesome',
                'parsley',
                'jquery-qtip',
                'jstree'), DUPLICATOR_PRO_VERSION);
            wp_register_style('dup-pro-import', DUPLICATOR_PRO_PLUGIN_URL.'assets/css/import.css', array('dup-pro-plugin-style', 'formstone'), DUPLICATOR_PRO_VERSION);

            //JS
            wp_register_script('dup-pro-handlebars', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/handlebars.min.js', array('jquery'), '4.0.10');
            wp_register_script('parsley', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/parsley.min.js', array('jquery'), '2.0.6');
            wp_register_script('jquery-qtip', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/jquery.qtip/jquery.qtip.min.js', array('jquery'), '3.0.3');
            wp_register_script('formstone', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/formstone/bundle.js', array('jquery'), 'v1.4.16-1');
            wp_register_script('jstree', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/jstree/jstree.min.js', array(), '3.3.7');
            wp_register_script('jscookie', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/jscookie/js.cookie.min.js', array(), '3.0.0');
            wp_register_script('dup-pro-import-installer', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/import-installer.js', array('jquery'), DUPLICATOR_PRO_VERSION, true);
        }
        if ($global->unhook_third_party_js || $global->unhook_third_party_css) {
            add_action('admin_enqueue_scripts', 'duplicator_pro_unhook_third_party_assets', 99999, 1);
        }

        add_action('admin_head', array('DUP_PRO_UI_Screen', 'getCustomCss'));
    }

    /**
     * Action Hook:
     * Hooked into `admin_menu`.  Loads all of the admin menus for DupPro
     *
     * @access global
     * @return null
     */
    function duplicator_pro_menu()
    {
        $wpfront_caps_translator = 'wpfront_user_role_editor_duplicator_pro_translate_capability';

        /* @var $global DUP_PRO_Global_Entity */
        $global = DUP_PRO_Global_Entity::get_instance();

        //Main Menu
        $perms_txt = 'export';
        $perms     = apply_filters($wpfront_caps_translator, $perms_txt);

        $main_menu = add_menu_page('Duplicator Plugin', 'Duplicator Pro', $perms, DUP_PRO_Constants::PLUGIN_SLUG, 'duplicator_pro_get_menu', DUP_PRO_Constants::ICON_SVG);

        $lang                              = DUP_PRO_U::__('Packages');
        $page_packages                     = add_submenu_page(DUP_PRO_Constants::PLUGIN_SLUG, $lang, $lang, $perms, DUP_PRO_Constants::$PACKAGES_SUBMENU_SLUG, 'duplicator_pro_get_menu');
        $GLOBALS['DUP_PRO_Package_Screen'] = new DUP_PRO_Package_Screen($page_packages);

        $perms_txt = 'manage_options';
        $perms     = apply_filters($wpfront_caps_translator, $perms_txt);

        if (!DUP_PRO_CTRL_import_installer::isDisallow()) {
            $page_import = add_submenu_page(
                DUP_PRO_Constants::PLUGIN_SLUG,
                DUP_PRO_U::__('Import'),
                DUP_PRO_U::__('Import'),
                $perms,
                DUP_PRO_Constants::$IMPORT_SUBMENU_SLUG, array('DUP_PRO_CTRL_import', 'controller'));
        }

        $lang           = DUP_PRO_U::__('Schedules');
        $page_schedules = add_submenu_page(DUP_PRO_Constants::PLUGIN_SLUG, $lang, $lang, $perms, DUP_PRO_Constants::$SCHEDULES_SUBMENU_SLUG, 'duplicator_pro_get_menu');

        $lang         = DUP_PRO_U::__('Storage');
        $page_storage = add_submenu_page(DUP_PRO_Constants::PLUGIN_SLUG, $lang, $lang, $perms, DUP_PRO_Constants::$STORAGE_SUBMENU_SLUG, 'duplicator_pro_get_menu');

        //$lang  = DUP_PRO_U::__('Templates');
        //$page_templates = add_submenu_page(DUP_PRO_Constants::PLUGIN_SLUG, $lang, $lang, $perms, DUP_PRO_Constants::$TEMPLATES_SUBMENU_SLUG, 'duplicator_pro_get_menu');

        $lang       = DUP_PRO_U::__('Tools');
        $page_tools = add_submenu_page(DUP_PRO_Constants::PLUGIN_SLUG, $lang, $lang, $perms, DUP_PRO_Constants::$TOOLS_SUBMENU_SLUG, 'duplicator_pro_get_menu');

        $lang          = DUP_PRO_U::__('Settings');
        $page_settings = add_submenu_page(DUP_PRO_Constants::PLUGIN_SLUG, $lang, $lang, $perms, DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG, 'duplicator_pro_get_menu');

        if ($global->debug_on) {
            $lang       = DUP_PRO_U::__('Debug');
            $page_debug = add_submenu_page(DUP_PRO_Constants::PLUGIN_SLUG, $lang, $lang, $perms, DUP_PRO_Constants::$DEBUG_SUBMENU_SLUG, 'duplicator_pro_get_menu');
            add_action('admin_print_scripts-'.$page_debug, 'duplicator_pro_scripts');
            add_action('admin_print_styles-'.$page_debug, 'duplicator_pro_styles');
        }

        // add page without add in menu
        if (!DUP_PRO_CTRL_import_installer::isDisallow()) {
            $page_installer = add_submenu_page(
                null,
                DUP_PRO_U::__('Install package'),
                '',
                $perms,
                DUP_PRO_Constants::$IMPORT_INSTALLER_PAGE, array('DUP_PRO_CTRL_import_installer', 'controller')
            );
        }

        //Apply Scripts
        add_action('admin_print_scripts-'.$page_packages, 'duplicator_pro_scripts');
        add_action('admin_print_scripts-'.$page_import, 'duplicator_pro_scripts');
        add_action('admin_print_scripts-'.$page_schedules, 'duplicator_pro_scripts');
        add_action('admin_print_scripts-'.$page_storage, 'duplicator_pro_scripts');
        add_action('admin_print_scripts-'.$page_settings, 'duplicator_pro_scripts');
        //add_action('admin_print_scripts-'.$page_templates, 'duplicator_pro_scripts');
        add_action('admin_print_scripts-'.$page_tools, 'duplicator_pro_scripts');
        add_action('admin_print_scripts-'.$page_installer, array('DUP_PRO_CTRL_import_installer', 'enqueueJs'), 99999, 1);

        //Apply Styles
        add_action('admin_print_styles-'.$page_packages, 'duplicator_pro_styles');
        add_action('admin_print_styles-'.$page_import, 'duplicator_pro_styles');
        add_action('admin_print_styles-'.$page_schedules, 'duplicator_pro_styles');
        add_action('admin_print_styles-'.$page_storage, 'duplicator_pro_styles');
        add_action('admin_print_styles-'.$page_settings, 'duplicator_pro_styles');
        // add_action('admin_print_styles-'.$page_templates, 'duplicator_pro_styles');
        add_action('admin_print_styles-'.$page_tools, 'duplicator_pro_styles');
        add_action('admin_print_styles-'.$page_installer, array('DUP_PRO_CTRL_import_installer', 'enqueueCss'));
    }

    /**
     * Menu Redirect:
     * Redirects the clicked menu item to the correct location
     *
     * @access global
     * @return null
     */
    function duplicator_pro_get_menu()
    {
        $current_page = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : DUP_PRO_Constants::$PACKAGES_SUBMENU_SLUG;

        switch ($current_page) {
            case DUP_PRO_Constants::$PACKAGES_SUBMENU_SLUG:
                require('views/packages/controller.php');
                break;
            case DUP_PRO_Constants::$SCHEDULES_SUBMENU_SLUG:
                require('views/schedules/controller.php');
                break;
            case DUP_PRO_Constants::$STORAGE_SUBMENU_SLUG:
                require('views/storage/controller.php');
                break;
            case DUP_PRO_Constants::$TEMPLATES_SUBMENU_SLUG:
                require('views/templates/controller.php');
                break;
            case DUP_PRO_Constants::$TOOLS_SUBMENU_SLUG:
                require('views/tools/controller.php');
                break;
            case DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG:
                require('views/settings/controller.php');
                break;
            case DUP_PRO_Constants::$DEBUG_SUBMENU_SLUG:
                require('debug/main.php');
                break;
            default:
                DUP_PRO_LOG::traceObject("Error current page doesnt show up", $_REQUEST);
        }
    }

    /**
     * Enqueue Scripts:
     * Loads all required javascript libs/source for DupPro
     *
     * @access global
     * @return null
     */
    function duplicator_pro_scripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-color');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-progressbar');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('parsley');
        wp_enqueue_script('accordion');
        wp_enqueue_script('jquery-qtip');
        wp_enqueue_script('formstone');
        wp_enqueue_script('jstree');
        wp_enqueue_script('jscookie');
    }

    /**
     * Hooked into `admin_enqueue_scripts`.  Init routines for all admin pages
     *
     * @access global
     * @return null
     */
    function duplicator_pro_admin_enqueue_scripts
    ()
    {
        wp_enqueue_script('dup-pro-global-script', DUPLICATOR_PRO_PLUGIN_URL.'assets/js/global-admin-script.js', array('jquery'), DUPLICATOR_PRO_VERSION, true);
        wp_localize_script('dup-pro-global-script',
            'dup_pro_global_script_data',
            array(
                'duplicator_pro_admin_notice_to_dismiss' => wp_create_nonce('duplicator_pro_admin_notice_to_dismiss'),
            )
        );
    }

    /**
     * Enqueue CSS Styles:
     * Loads all CSS style libs/source for DupPro
     *
     * @access global
     * @return null
     */
    function duplicator_pro_styles()
    {
        wp_enqueue_style('dup-pro-plugin-style');

        if (DUP_PRO_CTRL_import::isImportPage()) {
            wp_enqueue_style('dup-pro-import');
        }

        if (DUP_PRO_CTRL_Tools::isToolPage()) {
            wp_enqueue_style('dup-pro-import');
        }
    }
    /** ========================================================
     * FILTERS
     * =====================================================  */
    if (is_multisite()) {
        add_filter('network_admin_plugin_action_links', 'duplicator_pro_manage_link', 10, 2);
        add_filter('network_admin_plugin_row_meta', 'duplicator_pro_meta_links', 10, 2);
    } else {
        add_filter('plugin_action_links', 'duplicator_pro_manage_link', 10, 2);
        add_filter('plugin_row_meta', 'duplicator_pro_meta_links', 10, 2);
    }

    /**
     * Plugin MetaData:
     * Adds the manage link in the plugins list 
     *
     * @access global
     * @return string The manage link in the plugins list 
     */
    function duplicator_pro_manage_link($links, $file)
    {
        static $this_plugin;

        if (!$this_plugin) {
            $this_plugin = plugin_basename(__FILE__);
        }

        if ($file == $this_plugin) {
            $url           = DUP_PRO_U::getMenuPageURL(DUP_PRO_Constants::PLUGIN_SLUG, false);
            $settings_link = "<a href='$url'>".DUP_PRO_U::__('Manage').'</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }

    /**
     * Plugin MetaData:
     * Adds links to the plugins manager page
     *
     * @access global
     * @return string The meta help link data for the plugins manager
     */
    function duplicator_pro_meta_links($links, $file)
    {
        $plugin = plugin_basename(__FILE__);
        if ($file == $plugin) {
            $help_url = DUP_PRO_U::getMenuPageURL(DUP_PRO_Constants::$TOOLS_SUBMENU_SLUG, false);
            $links[]  = sprintf('<a href="%1$s" title="%2$s">%3$s</a>', esc_url($help_url), DUP_PRO_U::__('Get Help'), DUP_PRO_U::__('Help'));

            return $links;
        }
        return $links;
    }
    if (!function_exists('duplicator_pro_unhook_third_party_assets')) {

        /**
         * Remove all external styles and scripts coming from other plugins
         * which may cause compatibility issue, especially with React
         *
         * @return void
         */
        function duplicator_pro_unhook_third_party_assets($hook)
        {
            /*
              $hook values in duplicator pro admin pages:
              toplevel_page_duplicator-pro
              duplicator-pro_page_duplicator-pro-schedules
              duplicator-pro_page_duplicator-pro-storage
              duplicator-pro_page_duplicator-pro-storage
              DUP_PRO_CTRL_Tools::PAGE_ID
              duplicator-pro_page_duplicator-pro-settings
             */
            if (strpos($hook, 'duplicator-pro') !== false) {
                $global = DUP_PRO_Global_Entity::get_instance();
                $assets = array();

                if ($global->unhook_third_party_css) {
                    $assets['styles'] = wp_styles();
                }

                if ($global->unhook_third_party_js) {
                    $assets['scripts'] = wp_scripts();
                }

                foreach ($assets as $type => $asset) {
                    foreach ($asset->registered as $handle => $dep) {
                        $src = $dep->src;
                        // test if the src is coming from /wp-admin/ or /wp-includes/ or /wp-fsqm-pro/.
                        if (
                            is_string($src) && // For some built-ins, $src is true|false
                            strpos($src, 'wp-admin') === false &&
                            strpos($src, 'wp-include') === false &&
                            // things below are specific to your plugin, so change them
                            strpos($src, 'duplicator-pro') === false &&
                            strpos($src, 'woocommerce') === false &&
                            strpos($src, 'jetpack') === false &&
                            strpos($src, 'debug-bar') === false
                        ) {
                            'scripts' === $type ? wp_dequeue_script($handle) : wp_dequeue_style($handle);
                        }
                    }
                }
            }
        }
    }

    if (!function_exists('duplicator_use_anonymous_function')) {

        /**
         * Whether use anonymous function or not
         *
         * @return boolean whether use anonymous function or not
         */
        function duplicator_use_anonymous_function()
        {
            return version_compare(PHP_VERSION, '7.0.0');
        }
    }
}
