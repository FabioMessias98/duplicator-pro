<?php
defined("ABSPATH") or die("");

DUP_PRO_Handler::init_error_handler();
DUP_PRO_U::hasCapability('export');

global $wpdb;

//COMMON HEADER DISPLAY
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/assets/js/javascript.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/views/inc.header.php');

$_REQUEST['action'] = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'main';

switch ($_REQUEST['action']) {
    case 'detail': $current_view = 'detail';
        break;
    default:
        $current_view = 'main';
        break;
}

$nonce = wp_create_nonce('duplicator_pro_download_installer');
?>

<script>
    jQuery(document).ready(function ($)
    {
        DupPro.Pack.DownloadInstaller = function (json)
        {
            var actionLocation = ajaxurl + '?action=duplicator_pro_download_installer&id=' + json.id + '&hash=' + json.hash + '&nonce=' + '<?php echo $nonce; ?>';
            location.href = actionLocation;
            return false;
        };

        DupPro.Pack.DownloadFile = function (json)
        {
            var link = document.createElement('a');
            link.className = "dpro-dnload-menu-item";
            link.target = "_blank";
            link.download = json.filename;
            link.href = json.url;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            return false;
        };
    });
</script>

<div class="wrap">
    <?php
    switch ($current_view) {
        case 'main' :
            include('main/controller.php');
            break;
        case 'detail' :
            include('details/controller.php');
            break;
    }
    ?>
</div>
<?php
require_once DUPLICATOR_PRO_PLUGIN_PATH.'/views/parts/ajax-loader.php';