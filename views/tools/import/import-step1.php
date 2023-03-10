<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

// @var $viewMode string // single | list
// @var $adminMessageViewModeSwtich bool

if (DUP_PRO_CTRL_import_installer::isDisallow()) {
    ?>
    <div class="dup-pro-import-header" >
        <h2 class="title">
            <i class="fas fa-arrow-alt-circle-down"></i> <?php DUP_PRO_U::esc_html_e("Import"); ?>
        </h2>
        <hr />
    </div>
    <p>
        <?php DUP_PRO_U::esc_html_e("The import function is disabled"); ?>
    </p>
    <?php
    return;
}

switch ($viewMode) {
    case DUP_PRO_CTRL_import::VIEW_MODE_ADVANCED:
        $viewModeClass = 'view-list-item';
        break;
    case DUP_PRO_CTRL_import::VIEW_MODE_BASIC:
    default:
        $viewModeClass = 'view-single-item';
        break;
}

if ($adminMessageViewModeSwtich) {
    require dirname(__FILE__).'/import-message-view-mode-switch.php';
}

if (DUP_PRO_Global_Entity::get_instance()->import_chunk_size == 0) {
    $footerChunkInfo = sprintf(DUP_PRO_U::__('Upload Chunk Size: <b>N/A</b>, Max File Size: <b>%s</b>.'), size_format(wp_max_upload_size()));
    $toolTipContent  = DUP_PRO_U::__('If you need to upload a larger file, go to Settings > Import and set Upload Chunk Size');
} else {
    $footerChunkInfo = sprintf(DUP_PRO_U::__('Upload Chunk Size: <b>%s</b>, Max File Size: <b>No Limit</b>.'), size_format(DUP_PRO_CTRL_import::getChunkSize() * 1024));
    $toolTipContent  = DUP_PRO_U::__('The maximum file size limit is bypassed when chunking is enabled. '
            .'With fast connections a large chunk size is recommended, while with slow connections a small chunk size is recommended.<br><br>'
            .'You can change the chunk size by going to Settings > Import');
}

$uploadTooltipInfo = DUP_PRO_U::__('The upload speed can be affected by various factors such as connection speed or server status.').'<br><br>';
$uploadTooltipInfo .= DUP_PRO_U::__('Additionally, chunk size can influence the upload speed (Settings > Import)<br/><br/>'
        .'If uploading is still slow after changing Upload Chunk Size, you may want to upload the archive manually instead:');
$uploadTooltipInfo .= '<ul>'.
    '<li>'.DUP_PRO_U::__('1) Cancel current upload').'</li>'.
    '<li>'.DUP_PRO_U::__('2) Manually upload archive to "wp-content/backups-dup-pro/imports" directory').'</li>'.
    '<li>'.DUP_PRO_U::__('3) Refresh the Tools > Import screen').'</li>'.
    '</ul>';
?>
<div class="dup-pro-import-header" >
    <h2 class="title">
        <i class="fas fa-arrow-alt-circle-down"></i> <?php printf(DUP_PRO_U::esc_html__("Step %s of 2: Upload Archive"), '<span class="red">1</span>'); ?>
    </h2>
    <div class="options" >
        <?php require dirname(__FILE__).'/import-views-and-options.php'; ?>
    </div>
    <hr />
</div>
<?php $packRowTemplate   = dirname(__FILE__).'/import-package-row.php'; ?>
<div id="dup-pro-import-upload-file" ></div>
<div class="no_display" >
    <div id="dup-pro-import-upload-file-content" >
        <i class="fa fa-upload fa-2x" ></i><br>
        <p class="message" >
            <?php DUP_PRO_U::esc_html_e("Drag & Drop to upload"); ?><br>
            <?php DUP_PRO_U::esc_html_e("Duplicator archive file."); ?>
        </p>
        <input id="dpro-step-1-btn" type="button" class="button button-large" name="dpro-files" id="dpro-daf-upload-btn" value="<?php DUP_PRO_U::esc_attr_e("Select File"); ?>">
        <p id="dup-pro-basic-mode-message" class="red <?php echo $viewMode == DUP_PRO_CTRL_import::VIEW_MODE_ADVANCED ? 'no-display' : ''; ?>">
            <?php DUP_PRO_U::esc_html_e('Only one archive can be uploaded in Basic Mode.'); ?><br>
            <?php DUP_PRO_U::esc_html_e('To upload multiple archive switch to "Advanced Mode" via the menu in the right.'); ?><br>
        </p>
    </div>
</div>
<p id="dup-pro-import-upload-file-footer" class="margin-bottom-2">
    <span class="link-style no-decoration" data-tooltip-title="Improve upload speed:" data-tooltip="<?php echo esc_attr($uploadTooltipInfo); ?>" >
        <?php DUP_PRO_U::esc_html_e('Slow Upload'); ?> <i class="fas fa-question-circle fa-sm" ></i>
    </span>
    <span class="float-right" >
        <i class="fas fa-question-circle fa-sm" data-tooltip-title="Chunk size:" data-tooltip="<?php echo esc_attr($toolTipContent); ?>" ></i> <?php echo $footerChunkInfo; ?>
    </span>
</p>
<div id="dpro-pro-import-available-packages" class="<?php echo $viewModeClass; ?>" >
    <table class="dup-pro-import-box packages-list">
        <thead>
            <tr>
                <th class="name"><?php DUP_PRO_U::esc_html_e("Available Archives"); ?></th>
                <th class="size"><?php DUP_PRO_U::esc_html_e("Size"); ?></th>
                <th class="created"><?php DUP_PRO_U::esc_html_e("Created"); ?></th>
                <th class="funcs">&nbsp;</th>
            </tr>
        </thead>
        <tbody> 
            <?php
            $importObjs        = DUP_PRO_Package_Importer::getArchiveObjects();
            if (count($importObjs) === 0) {
                require dirname(__FILE__).'/import-package-row-no-found.php';
            } else {
                foreach ($importObjs as $importObj) {
                    require $packRowTemplate;
                }
                $importObj = null;
            }
            ?>
        </tbody>
    </table>
    <div class="no_display" >
        <table id="dup-pro-import-available-packages-templates">
            <?php
            $idRow = 'dup-pro-import-row-template';
            require $packRowTemplate;
            require dirname(__FILE__).'/import-package-row-no-found.php';
            ?>
        </table>
    </div>
</div>
