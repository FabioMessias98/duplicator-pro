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

abstract class DUPX_Validation_abstract_item
{

    const LV_FAIL         = 0;
    const LV_HARD_WARNING = 1;
    const LV_SOFT_WARNING = 2;
    const LV_GOOD         = 3;
    const LV_PASS         = 4;
    const LV_SKIP         = 1000;

    protected $category   = '';
    protected $testResult = null;

    public function __construct($category = '')
    {
        $this->category = $category;
    }

    public function test($reset = false)
    {
        if ($reset || is_null($this->testResult)) {
            try {
                DUPX_Log::info('BEFORE TEST '.get_called_class(), DUPX_Log::LV_DEBUG);
                $this->testResult = $this->runTest();
                DUPX_Log::info('TEST '.get_called_class().' result: '.$this->resultString());
            }
            catch (Exception $e) {
                DUPX_Log::logException($e, DUPX_Log::LV_DEFAULT, 'VALIDATION TEST '.get_called_class().' EXCEPTION: ');
                $this->testResult = self::LV_FAIL;
            }
            catch (Error $e) {
                DUPX_Log::logException($e, DUPX_Log::LV_DEFAULT, 'VALIDATION TEST '.get_called_class().' ERROR: ');
                $this->testResult = self::LV_FAIL;
            }
        }

        return $this->testResult;
    }

    abstract protected function runTest();

    public function display()
    {
        if ($this->testResult === self::LV_SKIP) {
            return false;
        } else {
            return true;
        }
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getTitle()
    {
        return 'Test class '.get_called_class();
    }

    public function getContent()
    {
        switch ($this->test(false)) {
            case self::LV_SKIP:
                return $this->skipContent();
            case self::LV_GOOD:
                return $this->goodContent();
            case self::LV_PASS:
                return $this->passContent();
            case self::LV_SOFT_WARNING:
                return $this->swarnContent();
            case self::LV_HARD_WARNING:
                return $this->hwarnContent();
            case self::LV_FAIL:
            default:
                return $this->failContent();
        }
    }

    public function getBadgeClass()
    {
        return self::resultLevelToBadgeClass($this->test(false));
    }

    public function resultString()
    {
        return self::resultLevelToString($this->test(false));
    }

    public static function resultLevelToString($level)
    {
        switch ($level) {
            case self::LV_SKIP:
                return 'skip';
            case self::LV_GOOD:
                return 'good';
            case self::LV_PASS:
                return 'passed';
            case self::LV_SOFT_WARNING:
                return 'soft warning';
            case self::LV_HARD_WARNING:
                return 'hard warning';
            case self::LV_FAIL:
            default:
                return 'failed';
        }
    }

    public static function resultLevelToBadgeClass($level)
    {
        switch ($level) {
            case self::LV_SKIP:
                return '';
            case self::LV_GOOD:
                return 'good';
            case self::LV_PASS:
                return 'pass';
            case self::LV_SOFT_WARNING:
                return 'warn';
            case self::LV_HARD_WARNING:
                return 'hwarn';
            case self::LV_FAIL:
            default:
                return 'fail';
        }
    }

    protected function failContent()
    {
        return 'test result: fail';
    }

    protected function hwarnContent()
    {
        return 'test result: hard warning';
    }

    protected function swarnContent()
    {
        return 'test result: soft warning';
    }

    protected function goodContent()
    {
        return 'test result: good';
    }

    protected function passContent()
    {
        return 'test result: pass';
    }

    protected function skipContent()
    {
        return 'test result: skipped';
    }
}