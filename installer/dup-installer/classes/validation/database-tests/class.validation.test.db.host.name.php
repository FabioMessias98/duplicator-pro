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

class DUPX_Validation_test_db_host_name extends DUPX_Validation_abstract_item
{

    protected $fixedHost = '';

    protected function runTest()
    {
        if (DUPX_Validation_database_service::getInstance()->skipDatabaseTests()) {
            return self::LV_SKIP;
        }

        $host             = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_HOST);
        //Host check
        $parsed_host_info = DUPX_DB::parseDBHost($host);
        $parsed_host      = $parsed_host_info[0];
        $isInvalidHost    = $parsed_host == 'http' || $parsed_host == "https";

        if ($isInvalidHost) {
            $this->fixedHost = DupProSnapLibIOU::untrailingslashit(str_replace($parsed_host."://", "", $host));
            DUPX_Validation_database_service::getInstance()->setSkipOtherTests();
            return self::LV_FAIL;
        } else {
            return self::LV_PASS;
        }
    }

    public function getTitle()
    {
        return 'Host name check';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-host-name', array(
            'isOk'      => false,
            'host'      => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_HOST),
            'fixedHost' => $this->fixedHost
            ), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/database-tests/db-host-name', array(
            'isOk'      => true,
            'host'      => DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_HOST),
            'fixedHost' => ''
            ), false);
    }
}