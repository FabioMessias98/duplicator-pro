<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<div id="header-main-wrapper" >
    <div class="dupx-logfile-link">
        <?php DUPX_View_Funcs::installerLogLink(); ?>
    </div>
    <div class="hdr-main">
        <?php echo $htmlTitle; ?>
    </div>
</div>