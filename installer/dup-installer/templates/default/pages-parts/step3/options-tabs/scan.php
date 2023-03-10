<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<div class="help-target">
    <?php DUPX_View_Funcs::helpIconLink('step3'); ?>
</div>
<div class="hdr-sub3">Database Scan Options</div>
<div  class="dupx-opts s3-opts">
    <?php
    if ($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE) === DUPX_S3_Funcs::MODE_SKIP) {
        ?>
        <p>
            <small>Restore backup mode is active so the search and replace option are disabled.</small>
        </p>
        <?php
    } else {

        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_EMPTY_SCHEDULE_STORAGE);
        $tableSelectId = $paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_TABLES);
        ?>
        <div class="param-wrapper" >
            <label for="<?php echo $paramsManager->getFormItemId(DUPX_Paramas_Manager::PARAM_DB_TABLES); ?>" >
                <b>Scan Tables:</b>
            </label>
            <div class="s3-allnonelinks">
                <a href="javascript:void(0)" onclick="$('#<?php echo $tableSelectId; ?> option').prop('selected', true);">[All]</a>
                <a href="javascript:void(0)" onclick="$('#<?php echo $tableSelectId; ?> option').prop('selected', false);">[None]</a>
            </div><br style="clear:both" />
            <?php $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DB_TABLES); ?>
        </div>
        <?php
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_EMAIL_REPLACE);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_FULL_SEARCH);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_MULTISITE_CROSS_SEARCH);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_POSTGUID);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_MAX_SERIALIZE_CHECK);
    }
    ?>
</div>