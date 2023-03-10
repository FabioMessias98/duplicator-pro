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

class DUPX_Validation_test_db_visibility extends DUPX_Validation_abstract_item
{

    protected $errorMessage = '';

    protected function runTest()
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        if (DUPX_Validation_database_service::getInstance()->checkDbVisibility($this->errorMessage)) {
            return self::LV_PASS;
        } else {
            DUPX_Validation_database_service::getInstance()->setSkipOtherTests();
            return self::LV_FAIL;
        }
    }

    public function getTitle()
    {
        return 'Confirm Database Visibility';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-visibility', array(
            'isOk'         => false,
            'databases'    => DUPX_Validation_database_service::getInstance()->getDatabases(),
            'dbname'       => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME),
            'dbuser'       => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_USER),
            'errorMessage' => $this->errorMessage
            ), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-visibility', array(
            'isOk'         => true,
            'databases'    => DUPX_Validation_database_service::getInstance()->getDatabases(),
            'dbname'       => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME),
            'dbuser'       => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_USER),
            'errorMessage' => $this->errorMessage
            ), false);
    }
}