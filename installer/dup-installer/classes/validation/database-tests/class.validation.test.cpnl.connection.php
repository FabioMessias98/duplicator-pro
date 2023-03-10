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

class DUPX_Validation_test_cpnl_connection extends DUPX_Validation_abstract_item
{

    protected function runTest()
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests() ||
            DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE) !== 'cpnl') {
            return self::LV_SKIP;
        }

        if (DUPX_Validation_database_service::getInstance()->getCpnlConnection() === false) {
            DUPX_Validation_database_service::getInstance()->setSkipOtherTests();
            return self::LV_FAIL;
        } else {
            return self::LV_PASS;
        }
    }

    public function getTitle()
    {
        return 'Cpanel connection';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/cpnl-connection', array(
            'isOk'     => false,
            'cpnlHost' => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_CPNL_HOST),
            'cpnlUser' => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_CPNL_USER),
            'cpnlPass' => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_CPNL_PASS)
            ), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/cpnl-connection', array(
            'isOk'     => true,
            'cpnlHost' => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_CPNL_HOST),
            'cpnlUser' => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_CPNL_USER),
            'cpnlPass' => '*****'
            ), false);
    }
}