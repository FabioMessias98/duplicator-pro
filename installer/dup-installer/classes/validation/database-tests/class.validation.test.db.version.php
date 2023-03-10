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

class DUPX_Validation_test_db_version extends DUPX_Validation_abstract_item
{

    protected $dbVersion = null;

    protected function runTest()
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        $this->dbVersion = DUPX_DB::getVersion(DUPX_Validation_database_service::getInstance()->getDbConnection());
        DUPX_Log::info('Database version '.DUPX_Log::varToString($this->dbVersion), DUPX_Log::LV_DETAILED);

        if (version_compare($this->dbVersion, '5.0.0', '<')) {
            DUPX_Validation_database_service::getInstance()->setSkipOtherTests();
            return self::LV_FAIL;
        } else {
            return self::LV_PASS;
        }
    }

    public function getTitle()
    {
        return 'Database version';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-version', array(
            'isOk'      => false,
            'dbVersion' => $this->dbVersion
            ), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-version', array(
            'isOk'      => true,
            'dbVersion' => $this->dbVersion
            ), false);
    }
}