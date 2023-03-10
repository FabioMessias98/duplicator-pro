<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<div id="s2-cpnl-db-opts" >
    <div class="hdr-sub3 database-setup-title">Database Connection</div>
    <p>
        <span id="s2-cpnl-db-opts-lbl">cPanel login required to enable</span>
    </p>
    <input type="hidden" name="cpnl-dbname-result" id="cpnl-dbname-result" />
    <input type="hidden" name="cpnl-dbuser-result" id="cpnl-dbuser-result" />
    <?php
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CPNL_IGNORE_PREFIX);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CPNL_DB_ACTION);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CPNL_DB_HOST);
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CPNL_PREFIX);
    ?>
    <div class="param-wrapper" >
        <?php
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_SEL);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_TXT);
        ?>
    </div>
    <div class="param-wrapper" >
        <?php
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_SEL);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_TXT);
        $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_CHK);
        ?>
    </div>
    <?php
    $paramsManager->getHtmlFormParam(DUPX_Paramas_Manager::PARAM_CPNL_DB_PASS);
    ?>
</div>
<?php
