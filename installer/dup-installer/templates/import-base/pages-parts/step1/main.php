<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<form id="s1-input-form" method="post" class="content-form" autocomplete="off" >
    <?php
    dupxTplRender('pages-parts/step1/import-info');
    if (DUPX_Conf_Utils::showMultisite()) {
        dupxTplRender('pages-parts/step1/base-setup');
    }
    dupxTplRender('parts/validation/validate-area');
    dupxTplRender('pages-parts/step1/actions-part');
    ?>
</form>
<?php
dupxTplRender('pages-parts/step1/proceed-confirm-dialog');
