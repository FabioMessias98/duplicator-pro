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

class DUPX_Validation_test_db_user_perms extends DUPX_Validation_abstract_item
{

    protected $perms         = array();
    protected $errorMessages = array();

    protected function runTest()
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        return DUPX_Validation_database_service::getInstance()->dbCheckUserPerms($this->perms, $this->errorMessages);
    }

    public function getTitle()
    {
        return 'Test User Table Privileges';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-user-perms', array(
            'testResult'          => self::LV_FAIL,
            'perms'         => $this->perms,
            'failedPerms'   => array_keys($this->perms, false, true),
            'dbname'        => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME),
            'dbuser'        => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_USER),
            'errorMessages' => $this->errorMessages
            ), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-user-perms', array(
            'testResult'          => self::LV_PASS,
            'perms'         => $this->perms,
            'dbname'        => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME),
            'dbuser'        => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_USER),
            'errorMessages' => $this->errorMessages
            ), false);
    }

    protected function hwarnContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-user-perms', array(
            'testResult'          => self::LV_HARD_WARNING,
            'perms'         => $this->perms,
            'dbname'        => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME),
            'dbuser'        => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_USER),
            'errorMessages' => $this->errorMessages
            ), false);
    }
}