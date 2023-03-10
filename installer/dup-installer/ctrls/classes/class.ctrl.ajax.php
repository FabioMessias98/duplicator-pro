<?php
/**
 * controller step 0
 * 
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

final class DUPX_Ctrl_ajax
{

    const DEBUG_AJAX_CALL_SLEEP            = 0;
    const PREVENT_BRUTE_FORCE_ATTACK_SLEEP = 2;
    const AJAX_NAME                        = 'ajax_request';
    const ACTION_NAME                      = 'ajax_action';
    const TOKEN_NAME                       = 'ajax_csrf_token';
    // ACCEPTED ACTIONS
    const ACTION_INITPASS_CHECK            = 'initpass';
    const ACTION_VALIDATE                  = 'validate';
    const ACTION_SET_PARAMS_S1             = 'sparam_s1';
    const ACTION_SET_PARAMS_S2             = 'sparam_s2';
    const ACTION_SET_PARAMS_S3             = 'sparam_s3';
    const ACTION_EXTRACTION                = 'extract';
    const ACTION_DBINSTALL                 = 'dbinstall';
    const ACTION_WEBSITE_UPDATE            = 'webupdate';
    const ACTION_PWD_CHECK                 = 'pwdcheck';
    const ACTION_FINAL_TESTS_PREPARE       = 'finalpre';
    const ACTION_FINAL_TESTS_AFTER         = 'finalafter';

    public static function ajaxActions()
    {
        static $actions = null;
        if (is_null($actions)) {
            $actions = array(
                self::ACTION_VALIDATE,
                self::ACTION_SET_PARAMS_S1,
                self::ACTION_SET_PARAMS_S2,
                self::ACTION_SET_PARAMS_S3,
                self::ACTION_EXTRACTION,
                self::ACTION_DBINSTALL,
                self::ACTION_WEBSITE_UPDATE,
                self::ACTION_PWD_CHECK,
                self::ACTION_FINAL_TESTS_PREPARE,
                self::ACTION_FINAL_TESTS_AFTER
            );
        }
        return $actions;
    }

    public static function controller()
    {
        $action = null;
        if (self::isAjax($action) === false) {
            return false;
        }

        ob_start();

        DUPX_Log::info("\n".'-------------------------'."\n".'AJAX ACTION ['.$action."] START");
        DUPX_Log::infoObject('POST DATA: ', $_POST, DUPX_Log::LV_DEBUG);

        $jsonResult = array(
            'success'      => true,
            'message'      => '',
            "errorContent" => array(
                'pre'  => '',
                'html' => ''
            ),
            'trace'        => '',
            'actionData'   => null
        );

        DUPX_Log::setThrowExceptionOnError(true);

        try {
            DUPX_Template::getInstance()->setTemplate(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_TEMPLATE));
            $jsonResult['actionData'] = self::actions($action);
        }
        catch (Exception $e) {
            DUPX_Log::logException($e);

            if (DupProSnapLibStringU::isHTML($e->getMessage())) {
                $message = $e->getMessage();
            } else {
                $message = DUPX_U::esc_html($e->getMessage());
            }

            $jsonResult = array(
                'success'      => false,
                'message'      => $message,
                "errorContent" => array(
                    'pre'  => $e->getTraceAsString(),
                    'html' => ''
                )
            );
        }

        $invalidOutput = '';
        while (ob_get_level() > 0) {
            $invalidOutput .= ob_get_clean();
        }
        if (!empty($invalidOutput)) {
            DUPX_Log::info('INVALID AJAX OUTPUT:'."\n".$invalidOutput."\n---------------------------------");
        }

        if ($jsonResult['success']) {
            DUPX_Log::info('AJAX ACTION ['.$action.'] SUCCESS');
        } else {
            DUPX_Log::info('AJAX ACTION ['.$action.'] FAIL, MESSAGE: '.$jsonResult['message']);
        }

        DUPX_Log::info('-------------------------'."\n");

        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo DupProSnapJsonU::wp_json_encode($jsonResult);
        DUPX_Log::close();
        // if is ajax always die;
        die();
    }

    /**
     * ajax actions 
     * 
     * @param string $action
     * @return mixed
     * @throws Exception
     */
    protected static function actions($action)
    {
        $actionData = null;

        self::debugAjaxCallSleep();

        switch ($action) {
            case self::ACTION_PWD_CHECK:
                $actionData = DUPX_Security::actionPasswordCheck();
                break;
            case self::ACTION_VALIDATE:
                DUP_PRO_Extraction::resetData();
                $actionData = DUPX_Validation_manager::getInstance()->getValidateData();
                if ($actionData['mainLevel'] <= DUPX_Validation_abstract_item::LV_FAIL) {
                    sleep(self::PREVENT_BRUTE_FORCE_ATTACK_SLEEP);
                }
                break;
            case self::ACTION_SET_PARAMS_S1:
                $valid          = DUPX_Ctrl_Params::setParamsStep1();
                DUPX_NOTICE_MANAGER::getInstance()->nextStepLog(false);
                $nexStepNotices = DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false);
                $reuslt         = array(
                    'isValid'              => $valid,
                    'nextStepMessagesHtml' => $nexStepNotices
                );

                $actionData     = $reuslt;
                break;
            case self::ACTION_SET_PARAMS_S2:
                $valid          = DUPX_Ctrl_Params::setParamsStep2();
                DUPX_NOTICE_MANAGER::getInstance()->nextStepLog(false);
                $nexStepNotices = DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false);
                $reuslt         = array(
                    'isValid'              => $valid,
                    'nextStepMessagesHtml' => $nexStepNotices
                );

                $actionData     = $reuslt;
                break;
            case self::ACTION_SET_PARAMS_S3:
                $valid          = DUPX_Ctrl_Params::setParamsStep3();
                DUPX_NOTICE_MANAGER::getInstance()->nextStepLog(false);
                $nexStepNotices = DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false);
                $reuslt         = array(
                    'isValid'              => $valid,
                    'nextStepMessagesHtml' => $nexStepNotices
                );

                $actionData = $reuslt;
                break;
            case self::ACTION_EXTRACTION:
                $extractor  = DUP_PRO_Extraction::getInstance();
                DUPX_U::maintenanceMode(true);
                $extractor->runExtraction();
                $actionData = $extractor->finishExtraction();
                break;
            case self::ACTION_DBINSTALL:
                $dbInstall  = DUPX_DBInstall::getInstance();
                $actionData = $dbInstall->deploy();
                DUPX_Plugins_Manager::getInstance()->preViewChecks();
                break;
            case self::ACTION_WEBSITE_UPDATE:
                $actionData = DUPX_S3_Funcs::getInstance()->updateWebsite();
                break;
            case self::ACTION_FINAL_TESTS_PREPARE:
                $actionData = DUPX_test_wordpress_exec::preTestPrepare();
                break;
            case self::ACTION_FINAL_TESTS_AFTER:
                $actionData = DUPX_test_wordpress_exec::afterTestClean();
                break;
            default:
                throw new Exception('Invalid ajax action');
        }
        return $actionData;
    }

    public static function isAjax(&$action = null)
    {
        static $isAjaxAction = null;
        if (is_null($isAjaxAction)) {
            $isAjaxAction = array(
                'isAjax' => false,
                'action' => false
            );

            $argsInput = filter_input_array(INPUT_POST, array(
                DUPX_Paramas_Manager::PARAM_CTRL_ACTION => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR | FILTER_FLAG_STRIP_HIGH,
                    'options' => array('default' => '')
                ),
                self::ACTION_NAME                       => array(
                    'filter'  => FILTER_SANITIZE_STRING,
                    'flags'   => FILTER_REQUIRE_SCALAR | FILTER_FLAG_STRIP_HIGH,
                    'options' => array('default' => false)
                )
            ));

            if ($argsInput[DUPX_Paramas_Manager::PARAM_CTRL_ACTION] !== 'ajax' || $argsInput[self::ACTION_NAME] === false) {
                $isAjaxAction['isAjax'] = false;
            } else {
                if (($isAjaxAction['isAjax'] = in_array($argsInput[self::ACTION_NAME], self::ajaxActions()))) {
                    $isAjaxAction['action'] = $argsInput[self::ACTION_NAME];
                }
            }
        }

        if ($isAjaxAction['isAjax']) {
            $action = $isAjaxAction['action'];
        }
        return $isAjaxAction['isAjax'];
    }

    public static function getTokenKeyByAction($action)
    {
        return self::ACTION_NAME.$action;
    }

    public static function getTokenFromInput()
    {
        return filter_input(INPUT_POST, self::TOKEN_NAME, FILTER_SANITIZE_STRING, array('default' => false));
    }

    public static function generateToken($action)
    {
        return DUPX_CSRF::generate(self::getTokenKeyByAction($action));
    }

    protected static function debugAjaxCallSleep()
    {
        if (self::DEBUG_AJAX_CALL_SLEEP > 0) {
            sleep(self::DEBUG_AJAX_CALL_SLEEP);
        }
    }
}