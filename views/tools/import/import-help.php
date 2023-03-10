<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<p>
    <?php DUP_PRO_U::esc_html_e("The import migration tool allows a Duplicator Pro package to be installed over this site.  This process consist  of the following steps:"); ?>
</p>
<ol>
    <li><?php DUP_PRO_U::esc_html_e("Upload a Duplicator zip/daf archive file below."); ?></li>
    <li><?php DUP_PRO_U::esc_html_e("Click the Launch Installer button and proceed with the install wizard."); ?></li>
    <li><?php DUP_PRO_U::esc_html_e("Upon install, this site will be overwritten with the uploaded archive files contents."); ?></li>
</ol>
<p>
    <?php 
        DUP_PRO_U::esc_html_e('For detailed instructions see ');
        echo '<a href="'.DUPLICATOR_PRO_DRAG_DROP_GUIDE_URL.'" target="_sc-ddguide">';
        DUP_PRO_U::esc_html_e('this article');
        echo '</a>.';
    ?>                                
</p>