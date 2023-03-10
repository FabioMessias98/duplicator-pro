<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE) === DUPX_S3_Funcs::MODE_SKIP && !$paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE)) {
    // IF IS FORCED MODE_SKIP the custom search and reaplace section is useless
    return;
}
?>
<div class="help-target">
    <?php DUPX_View_Funcs::helpIconLink('step3'); ?>
</div>

<?php if (DUPX_MU::newSiteIsMultisite()) { ?>
    <div class="margin-bottom-2" >
        <div class="hdr-sub3">Network replace</div>  
        <?php
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_REPLACE_MODE);
        dupxTplRender('pages-parts/step3/urls-mapping');
        ?>
    </div>
<?php }
?>

<div class="hdr-sub3">Custom Search &amp; Replace</div>
<table class="s3-opts" id="search-replace-table">
    <tr valign="top" id="search-0">
        <td>Search:</td>
        <td><input class="w95" type="text" name="search[]" style="margin-right:5px"></td>
    </tr>
    <tr valign="top" id="replace-0"><td>Replace:</td><td><input class="w95" type="text" name="replace[]"></td></tr>
</table>
<button type="button" onclick="DUPX.addSearchReplace();return false;" style="font-size:12px;display: block; margin: 10px 0 0 0; " class="default-btn">Add More</button>
