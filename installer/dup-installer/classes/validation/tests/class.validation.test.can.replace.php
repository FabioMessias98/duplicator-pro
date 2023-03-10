<?php
/**
 * Validation object
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_Validation_test_can_replace extends DUPX_Validation_abstract_item
{

    protected function runTest()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $replaceEngine = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE);
        if ($paramsManager->isHtmlInput(DUPX_Paramas_Manager::PARAM_REPLACE_ENGINE) || $replaceEngine !== DUPX_S3_Funcs::MODE_SKIP) {
            return self::LV_PASS;
        } else {
            if (DUPX_InstallerState::getInstance()->isInstallerCreatedInThisLocation()) {
                return self::LV_PASS;
            } else {
                return self::LV_FAIL;
            }
        }
    }

    public function getTitle()
    {
        return 'Install Package';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/tests/can-replace', array(), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/tests/can-replace', array(), false);
    }
}