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

class DUPX_Validation_test_iswritable extends DUPX_Validation_abstract_item
{

    protected function runTest()
    {
        $isWritableRes = self::getIsWritableRes();
        if ($isWritableRes['ret']) {
            return self::LV_PASS;
        } else {
            if (DUPX_InstallerState::isRecoveryMode() || DUPX_Custom_Host_Manager::getInstance()->isManaged()) {
                return self::LV_SOFT_WARNING;
            } else {
                return self::LV_HARD_WARNING;
            }
        }
    }

    protected static function getIsWritableRes()
    {
        static $isWritableRes = null;
        if (is_null($isWritableRes)) {
            $isWritableRes = DUPX_Server::is_dir_writable(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW));
        }
        return $isWritableRes;
    }

    public function getTitle()
    {
        return 'Permissions';
    }

    protected function hwarnContent()
    {
        $isWritableRes = self::getIsWritableRes();

        $result = dupxTplRender('parts/validation/tests/is-writable/info', array(), false);
        $result .= dupxTplRender('parts/validation/tests/is-writable/failed-objects', array(
            'failedObjects' => $isWritableRes['failedObjects']
            ), false);

        return $result;
    }

    protected function swarnContent()
    {
        return $this->hwarnContent();
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/tests/is-writable/info', array(), false);
    }
}