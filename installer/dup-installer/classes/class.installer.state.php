<?php
defined("DUPXABSPATH") or die("");

class DUPX_InstallerState
{

    const MODE_UNKNOWN     = -1;
    const MODE_STD_INSTALL = 0;
    const MODE_OVR_INSTALL = 1;
    const MODE_BK_RESTORE  = 2;

    /**
     *
     * @var int
     */
    protected $mode = self::MODE_UNKNOWN;

    /**
     *
     * @var string 
     */
    protected $ovr_wp_content_dir = '';

    /**
     *
     * @var self
     */
    private static $instance = null;

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        
    }

    /**
     * return installer mode
     * 
     * @return int 
     */
    public function getMode()
    {
        return DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_INSTALLER_MODE);
    }

    /**
     * check current installer mode 
     * 
     * @param bool $onlyIfUnknown // check se state only if is unknow state
     * @param bool $saveParams // if true update params
     * @return boolean
     */
    public function checkState($onlyIfUnknown = true, $saveParams = true)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();

        if ($onlyIfUnknown && $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_INSTALLER_MODE) !== self::MODE_UNKNOWN) {
            return true;
        }
        $isOverwrite = false;
        $nManager    = DUPX_NOTICE_MANAGER::getInstance();
        try {
            if (self::isImportFromBackendMode() || self::isRecoveryMode()) {
                $overwriteData = $this->getOverwriteDataFromParams();
            } else {
                $overwriteData = $this->getOverwriteDataFromWpConfig();
            }

            if (!empty($overwriteData)) {
                if (!DUPX_DB::testConnection($overwriteData['dbhost'], $overwriteData['dbuser'], $overwriteData['dbpass'], $overwriteData['dbname'])) {
                    throw new Exception('wp-config.php exists but database data connection isn\'t valid. Continuing with standard install');
                }
                $isOverwrite = true;

                $overwriteData['adminUsers'] = $this->getAdminUsersOnOverwriteDatabase($overwriteData);
            }
        }
        catch (Exception $e) {
            DUPX_Log::logException($e);
            $longMsg = "Exception message: ".$e->getMessage()."\n\n";
            $nManager->addNextStepNotice(array(
                'shortMsg'    => 'wp-config.php exists but isn\'t valid. Continue on standard install.',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE
            ));
            $nManager->saveNotices();
        }
        catch (Error $e) {
            DUPX_Log::logException($e);
            $longMsg = "Exception message: ".$e->getMessage()."\n\n";
            $nManager->addNextStepNotice(array(
                'shortMsg'    => 'wp-config.php exists but isn\'t valid. Continue on standard install.',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE
            ));
            $nManager->saveNotices();
        }


        if ($isOverwrite) {
            $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_INSTALLER_MODE, self::MODE_OVR_INSTALL);
            $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA, $overwriteData);
        } else {
            $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_INSTALLER_MODE, self::MODE_STD_INSTALL);
        }

        if ($saveParams) {
            return $this->save();
        } else {
            return true;
        }
    }

    protected function getOverwriteDataFromParams()
    {
        $overwriteData = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
        if (empty($overwriteData)) {
            return false;
        }

        if (!isset($overwriteData['dbhost']) || !isset($overwriteData['dbname']) || !isset($overwriteData['dbuser']) || !isset($overwriteData['dbpass'])) {
            return false;
        }

        return $overwriteData;
    }

    protected function getOverwriteDataFromWpConfig()
    {
        if (($wpConfigPath = DUPX_ServerConfig::getWpConfigLocalStoredPath()) === false) {
            $wpConfigPath = DUPX_WPConfig::getWpConfigPath();
            if (!file_exists($wpConfigPath)) {
                $wpConfigPath = DUPX_WPConfig::getWpConfigDeafultPath();
            }
        }

        $overwriteData = false;

        DUPX_Log::info('CHECK STATE INSTALLER WP CONFIG PATH: '.DUPX_Log::varToString($wpConfigPath), DUPX_Log::LV_DETAILED);

        if (!file_exists($wpConfigPath)) {
            return $overwriteData;
        }
        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        try {
            if (DUPX_WPConfig::getLocalConfigTransformer() === false) {
                throw new Exception('wp-config.php exist but isn\'t valid. continue on standard install');
            } else {
                $overwriteData = array(
                    'dbhost'       => DUPX_WPConfig::getValueFromLocalWpConfig('DB_HOST'),
                    'dbname'       => DUPX_WPConfig::getValueFromLocalWpConfig('DB_NAME'),
                    'dbuser'       => DUPX_WPConfig::getValueFromLocalWpConfig('DB_USER'),
                    'dbpass'       => DUPX_WPConfig::getValueFromLocalWpConfig('DB_PASSWORD'),
                    'table_prefix' => DUPX_WPConfig::getValueFromLocalWpConfig('table_prefix', 'variable'),
                    'isMultisite'  => DUPX_WPConfig::getValueFromLocalWpConfig('MULTISITE', 'constant', false),
                    'adminUsers'   => array()
                );
            }
        }
        catch (Exception $e) {
            $overwriteData = false;
            DUPX_Log::logException($e);
            $longMsg       = "Exception message: ".$e->getMessage()."\n\n";
            $nManager->addNextStepNotice(array(
                'shortMsg'    => 'wp-config.php exists but isn\'t valid. Continue on standard install.',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE
            ));
            $nManager->saveNotices();
        }
        catch (Error $e) {
            $overwriteData = false;
            DUPX_Log::logException($e);
            $longMsg       = "Exception message: ".$e->getMessage()."\n\n";
            $nManager->addNextStepNotice(array(
                'shortMsg'    => 'wp-config.php exists but isn\'t valid. Continue on standard install.',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE
            ));
            $nManager->saveNotices();
        }

        return $overwriteData;
    }

    protected function checkStateFromBackendImport()
    {
        
    }

    public static function isRecoveryMode()
    {
        return DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_TEMPLATE) === DUPX_Template::TEMPLATE_RECOVERY;
    }

    public static function isImportFromBackendMode()
    {
        return DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_TEMPLATE) === DUPX_Template::TEMPLATE_IMPORT_BASE;
    }

    public static function isAdvacnedMode()
    {
        return DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_TEMPLATE) === DUPX_Template::TEMPLATE_ADVANCED;
    }

    /**
     * this function in case of an error returns an empty array but never generates exceptions
     * 
     * @param string $overwriteData
     * @return array
     */
    protected function getAdminUsersOnOverwriteDatabase($overwriteData)
    {
        $adminUsers = array();
        try {
            $dbFuncs = DUPX_DB_Functions::getInstance();

            if (!$dbFuncs->dbConnection($overwriteData)) {
                DUPX_Log::info('GET USERS ON CURRENT DATABASE FAILED. Can\'t connect');
                return $adminUsers;
            }

            $usersTables = array(
                $dbFuncs->getUserTableName($overwriteData['table_prefix']),
                $dbFuncs->getUserMetaTableName($overwriteData['table_prefix'])
            );

            if (!$dbFuncs->tablesExist($usersTables)) {
                DUPX_Log::info('GET USERS ON CURRENT DATABASE FAILED. Users tables don\'t exists, continue with orverwrite installation but with option keep users disabled'."\n");
                $dbFuncs->closeDbConnection();
                return $adminUsers;
            }

            if (($adminUsers = $dbFuncs->getAdminUsers($overwriteData['table_prefix'])) === false) {
                DUPX_Log::info('GET USERS ON CURRENT DATABASE FAILED. OVERWRITE DB USERS NOT FOUND');
                $dbFuncs->closeDbConnection();
                return $adminUsers;
            }

            $dbFuncs->closeDbConnection();
        }
        catch (Exception $e) {
            DUPX_Log::logException($e, DUPX_Log::LV_DEFAULT, 'GET ADMIN USER EXECPTION BUT CONTINUE');
        }
        catch (Error $e) {
            DUPX_Log::logException($e, DUPX_Log::LV_DEFAULT, 'GET ADMIN USER EXECPTION BUT CONTINUE');
        }

        return $adminUsers;
    }

    /**
     * 
     * if (DUPX_InstallerState::getInstance()->getMode() === DUPX_InstallerState::MODE_OVR_INSTALL) {
      echo "<span class='dupx-overwrite'>Mode: Overwrite Install {$db_only_txt}</span>";
      } else {
      echo "Mode: Standard Install {$db_only_txt}";
      }
     */
    public function getHtmlModeHeader()
    {
        $php_enforced_txt = ($GLOBALS['DUPX_ENFORCE_PHP_INI']) ? '<i style="color:red"><br/>*PHP ini enforced*</i>' : '';
        $db_only_txt      = ($GLOBALS['DUPX_AC']->exportOnlyDB) ? ' - Database Only' : '';
        $db_only_txt      = $db_only_txt.$php_enforced_txt;

        switch ($this->getMode()) {
            case self::MODE_OVR_INSTALL:
                $label = 'Overwrite Install';
                $class = 'dupx-overwrite mode_overwrite';
                break;
            case self::MODE_STD_INSTALL:
                $label = 'Standard Install';
                $class = 'dupx-overwrite mode_standard';
                break;
            case self::MODE_BK_RESTORE:
                $label = 'Restore backup';
                $class = 'mode_restore_bk';
                break;
            case self::MODE_UNKNOWN:
            default:
                $label = 'Unknown';
                $class = 'mode_unknown';
                break;
        }
        
        if (strlen($db_only_txt)) {
            return '<span class="'.$class.'">['.$label.' '.$db_only_txt.']</span>';
        } else {
            return "<span class=\"{$class}\">[{$label}]</span>";
        }
    }

    /**
     * reset current mode
     * 
     * @param boolean $saveParams
     * @return boolean
     */
    public function resetState($saveParams = true)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_INSTALLER_MODE, self::MODE_UNKNOWN);
        if ($saveParams) {
            return $this->save();
        } else {
            return true;
        }
    }

    /**
     * save current installer state
     * 
     * @return bool
     * @throws Exception if fail
     */
    public function save()
    {
        return DUPX_Paramas_Manager::getInstance()->save();
    }

    /**
     * this function returns true if both the URL and path old and new path are identical
     * 
     * @return bool
     */
    public function isInstallerCreatedInThisLocation()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $originalPaths = DUPX_ArchiveConfig::getInstance()->getRealValue('originalPaths');

        $path_old_original = $originalPaths->home;
        $path_new          = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW);
        $path_old          = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_OLD);
        $url_new           = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW);
        $url_old           = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_OLD);
        return (($path_new === $path_old || $path_new === $path_old_original) && $url_new === $url_old);
    }
}