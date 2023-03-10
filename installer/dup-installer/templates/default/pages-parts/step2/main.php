<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>

<form id='s2-input-form' method="post" class="content-form"  autocomplete="off" data-parsley-validate="true" data-parsley-excluded="input[type=hidden], [disabled], :hidden">
    <div class="main-form-content" >
        <div class="hdr-sub1" id="s2-opts-hdr-basic" >
            Options
        </div>
        <div id="s2-opts-basic" class="hdr-sub1-area s2-opts">
            <div class="help-target">
                <?php DUPX_View_Funcs::helpIconLink('step2'); ?>
            </div>

            <div class="dupx-opts dupx-advopts">
                <?php
                $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_BLOGNAME);
                $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS);
                $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DB_SPACING);
                ?>
                <div class="param-wrapper" >
                    <?php
                    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE);
                    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS);
                    ?>
                </div>
                <?php
                $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DB_VIEW_CREATION);
                $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_DB_PROC_CREATION);
                ?>
            </div>
        </div>
    </div>
    <div class="footer-buttons margin-top-2">
        <div class="content-left">
        </div>
        <div class="content-right" >
            <button id="s2-next-btn-basic" type="button" onclick="DUPX.runDeployment()" class="default-btn">
                Next <i class="fa fa-caret-right"></i>
            </button>
        </div>
    </div>
</form>
