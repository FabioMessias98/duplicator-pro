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

class DUPX_Validation_test_managed_supported extends DUPX_Validation_abstract_item
{
    private $managed     = false;
    private $failMessage = '';

    protected function runTest()
    {
        if (!($this->managed = DUPX_Custom_Host_Manager::getInstance()->isManaged())) {
            return self::LV_SKIP;
        }
        
        if (DUPX_MU::newSiteIsMultisite()) {
            $this->failMessage = "Installing multisites on managed hosts is not supported";
            return self::LV_FAIL;
        }

        if ($this->managed === DUPX_Custom_Host_Manager::HOST_WORDPRESSCOM || $this->managed === DUPX_Custom_Host_Manager::HOST_PANTHEON) {
            if (DUPX_InstallerState::isImportFromBackendMode() || DUPX_InstallerState::isRecoveryMode()) {
                return self::LV_PASS;
            } else {
                $this->failMessage = "This managed hosting is not supported yet.";
                return self::LV_FAIL;
            }
        } else {
            return self::LV_PASS;
        }
    }

    public function getTitle()
    {
        return 'Managed hosting supported';
    }

    protected function failContent()
    {
        return dupxTplRender('parts/validation/tests/managed-supported', array(
            'isOk'           => false,
            'managedHosting' => $this->managed,
            'failMessage'    => $this->failMessage
            ),
            false);
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/tests/managed-supported', array(
            'isOk'           => true,
            'managedHosting' => $this->managed,
            'failMessage'    => $this->failMessage
            ),
            false);
    }
}