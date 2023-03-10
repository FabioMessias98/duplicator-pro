<?php
if (!defined('DUPXABSPATH')) {
    define('DUPXABSPATH', dirname(__FILE__));
}

define('DUPX_INIT', str_replace('\\', '/', dirname(__DIR__)));
define('DUPX_ROOT', preg_match('/^[\\\\\/]?$/', dirname(DUPX_INIT)) ? '/' : dirname(DUPX_INIT));

require_once(DUPX_INIT.'/classes/config/class.boot.php');
/**
 * init constants and include
 */
DUPX_Boot::$dupInitFolderParentLevel = 2;
DUPX_Boot::init();

require_once('class.api.php');
require_once('class.cpnl.base.php');
require_once('class.cpnl.ctrl.php');

//Register API Engine - If it processes the current route it spits out JSON and exits the process
$API_Server = new DUPX_API_Server();
$API_Server->add_controller(new DUPX_cPanel_Controller());
$API_Server->process_request(false);

dupxTplRender('api/front', array(
    'apiControllers' => $API_Server->controllers,
    'dupVersion'     => DUPX_ArchiveConfig::getInstance()->version_dup
));
