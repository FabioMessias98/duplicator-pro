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

class DUPX_Validation_test_recovery extends DUPX_Validation_abstract_item
{

    protected $importSiteInfo      = array();
    protected $recoveryPage        = false;
    protected $importPage          = false;
    protected $recoveryIsOutToDate = false;
    protected $recoveryPackageLife = 0;

    protected function runTest()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        if (!DUPX_InstallerState::isImportFromBackendMode()) {
            return self::LV_SKIP;
        }
        $this->importSiteInfo      = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_FROM_SITE_IMPORT_INFO);
        $this->importPage          = isset($this->importSiteInfo['import_page']) ? $this->importSiteInfo['import_page'] : false;
        $this->recoveryPage        = isset($this->importSiteInfo['recovery_page']) ? $this->importSiteInfo['recovery_page'] : false;
        $this->recoveryIsOutToDate = isset($this->importSiteInfo['recovery_is_out_to_date']) ? $this->importSiteInfo['recovery_is_out_to_date'] : false;
        $this->recoveryPackageLife = isset($this->importSiteInfo['recovery_package_life']) ? $this->importSiteInfo['recovery_package_life'] : 0;

        $recoveryLink = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_RECOVERY_LINK);
        if (empty($recoveryLink)) {
            return self::LV_HARD_WARNING;
        } else {
            if ($this->importSiteInfo['recovery_is_out_to_date']) {
                return self::LV_SOFT_WARNING;
            } else {
                return self::LV_GOOD;
            }
        }
    }

    public function getTitle()
    {
        return 'Recovery URL';
    }

    protected function hwarnContent()
    {
        return dupxTplRender('parts/validation/tests/recovery', array(
            'testResult'          => $this->testResult,
            'importPage'          => $this->importPage,
            'recoveryPage'        => $this->recoveryPage,
            'recoveryIsOutToDate' => $this->recoveryIsOutToDate,
            'recoveryPackageLife' => $this->recoveryPackageLife
            ), false);
    }

    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/tests/recovery', array(
            'testResult'          => $this->testResult,
            'importPage'          => $this->importPage,
            'recoveryPage'        => $this->recoveryPage,
            'recoveryIsOutToDate' => $this->recoveryIsOutToDate,
            'recoveryPackageLife' => $this->recoveryPackageLife
            ), false);
    }

    protected function goodContent()
    {
        return dupxTplRender('parts/validation/tests/recovery', array(
            'testResult'          => $this->testResult,
            'importPage'          => $this->importPage,
            'recoveryPage'        => $this->recoveryPage,
            'recoveryIsOutToDate' => $this->recoveryIsOutToDate,
            'recoveryPackageLife' => $this->recoveryPackageLife
            ), false);
    }
}