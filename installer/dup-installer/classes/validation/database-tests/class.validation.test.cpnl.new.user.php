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

class DUPX_Validation_test_cpnl_new_user extends DUPX_Validation_abstract_item
{

    private $user = null;

    protected function runTest()
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests() ||
            DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE) !== 'cpnl' ||
            DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_CHK) != true) {
            return self::LV_SKIP;
        }

        if ((DUPX_Validation_database_service::getInstance()->cpnlCreateDbUser($this->user)) === false) {
            DUPX_Validation_database_service::getInstance()->setSkipOtherTests();
            return self::LV_FAIL;
        } else {
            return self::LV_PASS;
        }
    }

    public function getTitle()
    {
        return 'Create Database User';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/cpnl-create-user', array(
            'isOk'        => false,
            'dbuser'      => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_USER),
            'dbpass'      => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_PASS),
            'errorMessage' => $this->user['status']
            ), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/cpnl-create-user', array(
            'isOk'        => true,
            'dbuser'      => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_USER),
            'dbpass'      => '*****',
            'errorMessage' => ''
            ), false);
    }
}