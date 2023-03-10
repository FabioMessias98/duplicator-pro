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

class DUPX_Validation_test_db_supported_charset extends DUPX_Validation_abstract_item
{

    protected $errorMessage = '';
    protected $extraData    = array();

    protected function runTest()
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        $result = DUPX_Validation_database_service::getInstance()->dbCheckCharset($this->extraData, $this->errorMessage);
        DUPX_Validation_manager::getInstance()->addExtraData('charsetTest', $this->extraData);

        if ($result) {
            return self::LV_PASS;
        } else {
            return self::LV_FAIL;
        }
    }

    public function getTitle()
    {
        return 'Check Character set and  Collation Capability';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-supported-charset', array(
            'isOk'         => false,
            'extraData'    => $this->extraData,
            'errorMessage' => $this->errorMessage
            ), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-supported-charset', array(
            'isOk'         => true,
            'extraData'    => $this->extraData,
            'errorMessage' => $this->errorMessage
            ), false);
    }
}