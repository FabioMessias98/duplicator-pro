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

class DUPX_Validation_test_db_create extends DUPX_Validation_abstract_item
{

    /**
     *
     * @var bool 
     */
    protected $alreadyExists = false;

    /**
     *
     * @var string 
     */
    protected $errorMessage = '';

    protected function runTest()
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests() ||
            DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_ACTION) !== DUPX_DBInstall::DBACTION_CREATE) {
            return self::LV_SKIP;
        }

        // already exists test
        if (DUPX_Validation_database_service::getInstance()->databaseExists()) {
            $this->errorMessage  = 'Database already exists';
            $this->alreadyExists = true;
            return self::LV_FAIL;
        }

        if (DUPX_Validation_database_service::getInstance()->createDatabase($this->errorMessage) === false) {
            return self::LV_FAIL;
        }
        
        return self::LV_PASS;
    }

    public function getTitle()
    {
        return 'Create New Database';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-create', array(
            'isOk'          => false,
            'alreadyExists' => $this->alreadyExists,
            'errorMessage'  => $this->errorMessage,
            'isCpanel'      => (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE) === 'cpnl'),
            'dbname'        => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME)
            ), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-create', array(
            'isOk'          => true,
            'alreadyExists' => $this->alreadyExists,
            'errorMessage'  => $this->errorMessage,
            'isCpanel'      => (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE) === 'cpnl'),
            'dbname'        => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_NAME)
            ), false);
    }
}