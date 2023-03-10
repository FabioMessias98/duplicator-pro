<?php
defined("ABSPATH") or die("");

require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/ctrls/ctrl.base.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/class.scan.check.php');

/**
 * Controller for Tools 
 */
class DUP_PRO_CTRL_Tools extends DUP_PRO_CTRL_Base
{

    const PAGE_ID         = 'duplicator-pro_page_duplicator-pro-tools';
    const PAGE_ID_NETWORK = 'duplicator-pro_page_duplicator-pro-tools-network';

    /**
     *  Init this instance of the object
     */
    function __construct()
    {
        add_action('wp_ajax_DUP_PRO_CTRL_Tools_runScanValidator', array($this, 'runScanValidator'));
    }

    public static function isToolPage()
    {
        if (!($screen = get_current_screen())) {
            return false;
        }
        return ($screen->id == self::PAGE_ID || $screen->id == self::PAGE_ID_NETWORK);
    }

    /**
     * Calls the ScanValidator and returns a JSON result
     * 
     * @param string $_POST['scan-path']		The path to start scanning from, defaults to DUPLICATOR_WPROOTPATH
     * @param bool   $_POST['scan-recursive]	Recursively  search the path
     * 
     * @notes: Testing = /wp-admin/admin-ajax.php?action=DUP_PRO_CTRL_Tools_runScanValidator
     */
    public function runScanValidator($post)
    {
        DUP_PRO_Handler::init_error_handler();
        check_ajax_referer('DUP_PRO_CTRL_Tools_runScanValidator', 'nonce');
        DUP_PRO_U::hasCapability('export');

        //@set_time_limit(0);
        // Let's setup execution time on proper way (multiserver supported)
        try {
            if (function_exists('set_time_limit'))
                set_time_limit(0); // unlimited
            else {
                if (function_exists('ini_set') && DupProSnapLibUtil::wp_is_ini_value_changeable('max_execution_time'))
                    ini_set('max_execution_time', 0); // unlimited
            }

            // there is error inside PHP because of PHP versions and server setup,
            // let's try to made small hack and set some "normal" value if is possible
        }
        catch (Exception $ex) {
            if (function_exists('set_time_limit'))
                @set_time_limit(3600); // 60 minutes
            else {
                if (function_exists('ini_set') && DupProSnapLibUtil::wp_is_ini_value_changeable('max_execution_time'))
                    @ini_set('max_execution_time', 3600); //  60 minutes
            }
        }

        $post = $this->postParamMerge($post);
        check_ajax_referer($post['action'], 'nonce');

        $result = new DUP_PRO_CTRL_Result($this);

        try {
            //CONTROLLER LOGIC
            $path = isset($post['scan-path']) ? $post['scan-path'] : duplicator_pro_get_home_path();
            if (!is_dir($path)) {
                throw new Exception("Invalid directory provided '{$path}'!");
            }
            $scanner            = new DUP_PRO_ScanValidator();
            $scanner->recursion = (isset($post['scan-recursive']) && $post['scan-recursive'] != 'false') ? true : false;
            $payload            = $scanner->run(DUP_PRO_Archive::getScanPaths());

            //RETURN RESULT
            $test = ($payload->fileCount > 0) ? DUP_PRO_CTRL_Status::SUCCESS : DUP_PRO_CTRL_Status::FAILED;
            $result->process($payload, $test);
        }
        catch (Exception $exc) {
            $result->processError($exc);
        }
    }
}