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

class DUPX_Validation_test_archive_check extends DUPX_Validation_abstract_item
{

    protected function runTest()
    {
        if (DUPX_Conf_Utils::isConfArkPresent()) {
            if (DUPX_Conf_Utils::archiveExists()) {
                return self::LV_PASS;
            } else {
                return self::LV_SOFT_WARNING;
            }
        } else {
            return self::LV_FAIL;
        }
    }

    public function getTitle()
    {
        return 'Archive check';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/tests/archive-check', array(
            'testResult' => $this->testResult
            ), false);
    }

    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/tests/archive-check', array(
            'testResult' => $this->testResult
            ), false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/tests/archive-check', array(
            'testResult' => $this->testResult
            ), false);
    }
}