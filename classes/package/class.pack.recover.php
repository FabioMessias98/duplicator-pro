<?php
/**
 * Class to import archive
 *
 * Standard: PSR-2 (almost)
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/package
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 1.0.0
 *
 * @notes: Trace process time
 * 	$timer01 = DUP_PRO_U::getMicrotime();
 * 	DUP_PRO_LOG::trace("SCAN TIME-B = " . DUP_PRO_U::elapsedTime(DUP_PRO_U::getMicrotime(), $timer01));
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUP_PRO_Package_Recover extends DUP_PRO_Package_Importer
{

    const MAX_PACKAGES_LIST         = 50;
    const OPTION_RECOVER_PACKAGE_ID = 'duplicator_pro_recover_point';
    const OUT_TO_HOURS_LIMIT        = 12;

    /**
     *
     * @var array 
     */
    protected static $recoveablesPackages = null;

    /**
     *
     * @var self 
     */
    protected static $instance = null;

    /**
     *
     * @var DUP_PRO_Package 
     */
    protected $package = null;

    /**
     * @note This constructor should be protected but I can't change visibility before php 7.3 so I have to leave it public..
     * Use getRecoverPackage to take a recover object. Don't init it directly
     * 
     * @param scrinf $path // valid archive patch
     * @throws Exception if file ins't valid
     */
    public function __construct($path, DUP_PRO_Package $package)
    {
        $this->package = $package;
        parent::__construct($path);
    }

    /**
     * 
     * @return int
     */
    public function getPackageId()
    {
        return $this->package->ID;
    }

    /**
     * in hours
     * 
     * @return int
     */
    public function getPackageLife()
    {
        $packageTime = strtotime($this->getCreated());
        $currentTime = strtotime('now');
        return ceil(($currentTime - $packageTime) / 60 / 60);
    }

    /**
     * 
     * @return bool
     */
    public function isOutToDate()
    {
        return $this->getPackageLife() > self::OUT_TO_HOURS_LIMIT;
    }

    protected function getInstallerFolderPath()
    {
        return DUPLICATOR_PRO_PATH_RECOVER;
    }

    protected function getInstallerFolderUrl()
    {
        return DUPLICATOR_PRO_URL_RECOVER;
    }

    public function getInstallLink()
    {
        if (dirname($this->archive) === DUPLICATOR_PRO_SSDIR_PATH) {
            $archive = '..';
        } else {
            $archive = dirname($this->archive);
        }

        $queryStr = http_build_query(array(
            'archive'    => $archive,
            'dup_folder' => 'dup-installer-'.$this->info->packInfo->secondaryHash
        ));
        return $this->getInstallerFolderUrl().'/'.$this->getInstallerName().'?'.$queryStr;
    }

    public function getLauncherFileName()
    {

        $parseUrl     = parse_url(get_home_url());
        $siteFileName = str_replace(array(':', '\\', '/', '.'), '_', $parseUrl['host'].$parseUrl['path']);
        sanitize_file_name($siteFileName);

        return 'recover_'.sanitize_file_name($siteFileName).'_'.date("Ymd_His", strtotime($this->getCreated())).'.html';
    }

    public function getOverwriteParams()
    {
        $params        = parent::getOverwriteParams();
        $updDirs       = wp_upload_dir();
        $recoverParams = array(
            'template'        => array(
                'value' => 'recovery',
            ),
            'recovery-link'   => array(
                'value' => '',
            ),
            'restore-backup'  => array(
                'value'      => true,
                'formStatus' => 'st_infoonly'
            ),
            'url_new'         => array(
                'value'      => home_url(),
                'formStatus' => 'st_infoonly'
            ),
            'path_new'        => array(
                'value'      => DUP_PRO_Archive::getOriginalPaths('home'),
                'formStatus' => 'st_infoonly'
            ),
            'siteurl'         => array(
                'value'      => site_url(),
                'formStatus' => 'st_infoonly'
            ),
            'path_core_new'   => array(
                'value'      => DUP_PRO_Archive::getOriginalPaths('abs'),
                'formStatus' => 'st_infoonly'
            ),
            'url_cont_new'    => array(
                'value'      => content_url(),
                'formStatus' => 'st_infoonly'
            ),
            'path_cont_new'   => array(
                'value'      => DUP_PRO_Archive::getOriginalPaths('wpcontent'),
                'formStatus' => 'st_infoonly'
            ),
            'url_upl_new'     => array(
                'value'      => $updDirs['baseurl'],
                'formStatus' => 'st_infoonly'
            ),
            'path_upl_new'    => array(
                'value'      => DUP_PRO_Archive::getOriginalPaths('uploads'),
                'formStatus' => 'st_infoonly'
            ),
            'url_plug_new'    => array(
                'value'      => plugins_url(),
                'formStatus' => 'st_infoonly'
            ),
            'path_plug_new'   => array(
                'value'      => DUP_PRO_Archive::getOriginalPaths('plugins'),
                'formStatus' => 'st_infoonly'
            ),
            'url_muplug_new'  => array(
                'value'      => WPMU_PLUGIN_URL,
                'formStatus' => 'st_infoonly'
            ),
            'path_muplug_new' => array(
                'value'      => DUP_PRO_Archive::getOriginalPaths('muplugins'),
                'formStatus' => 'st_infoonly'
            )
        );
        return array_merge($params, $recoverParams);
    }

    /**
     * 
     * @param boolean $reset
     * @return boolean|DUP_PRO_Package_Recover  // return false if recover package isn't set or recove package object 
     */
    public static function getRecoverPackage($reset = false)
    {
        if (is_null(self::$instance) || $reset) {
            if (($packageId = get_option(self::OPTION_RECOVER_PACKAGE_ID)) == false) {
                self::$instance = null;
                return false;
            }

            if (!self::isPackageIdRecoveable($packageId, $reset)) {
                self::$instance = null;
                return false;
            }

            $package        = DUP_PRO_Package::get_by_id($packageId);
            $archivePath    = $package->get_local_package_file(DUP_PRO_Package_File_Type::Archive);
            self::$instance = new self($archivePath, $package);
        }

        return self::$instance;
    }

    /**
     * 
     * @return boolean|int return false if not set or package id
     */
    public static function getRecoverPackageId()
    {
        $recoverPackage = DUP_PRO_Package_Recover::getRecoverPackage();
        if ($recoverPackage instanceof DUP_PRO_Package_Recover) {
            return $recoverPackage->getPackageId();
        } else {
            return false;
        }
    }

    /**
     * 
     * @param bool $emptyDir // if true remove recovery paackage files
     */
    public static function resetRecoverPackage($emptyDir = false)
    {
        self::$instance = null;
        if ($emptyDir) {
            DupProSnapLibIOU::emptyDir(DUPLICATOR_PRO_PATH_RECOVER);
        }
        delete_option(self::OPTION_RECOVER_PACKAGE_ID);
    }

    /**
     * 
     * @param int $id // if mepty reset package
     * 
     * @return boolean // false if fail
     */
    public static function setRecoveablePackage($id, &$errorMessage = null)
    {
        $id = (int) $id;

        self::resetRecoverPackage(true);

        if (empty($id)) {
            return true;
        }

        try {
            if (!self::isPackageIdRecoveable($id, true)) {
                throw Exception('Package isn\'t in recoveable list');
            }

            if (!DupProSnapLibIOU::mkdir(DUPLICATOR_PRO_PATH_RECOVER, 0755, true)) {
                throw new Exception('Can\'t create recover package folder');
            }

            if (!update_option(self::OPTION_RECOVER_PACKAGE_ID, $id)) {
                delete_option(self::OPTION_RECOVER_PACKAGE_ID);
                throw Exception('Can\'t update '.self::OPTION_RECOVER_PACKAGE_ID.' option');
            }

            $recoverPackage = DUP_PRO_Package_Recover::getRecoverPackage();
            if (!$recoverPackage instanceof DUP_PRO_Package_Recover) {
                throw new Exception('Can\'t inizialize recover package');
            }

            $recoverPackage->prepareToInstall();
        }
        catch (Exception $e) {
            delete_option(self::OPTION_RECOVER_PACKAGE_ID);
            $errorMessage = $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * 
     * @return array // id / name
     */
    public static function getRecoverablesPackages($reset = false)
    {
        if (is_null(self::$recoveablesPackages) || $reset) {
            self::$recoveablesPackages = array();
            DUP_PRO_Package::by_status_callback(array(__CLASS__, 'recoverablePackageCheck'), array(
                array('op' => '>=', 'status' => DUP_PRO_PackageStatus::COMPLETE)
                ), self::MAX_PACKAGES_LIST, 0, '`created` DESC');
        }
        self::addRecoverPackageToListIfNotExists();

        return self::$recoveablesPackages;
    }

    protected static function addRecoverPackageToListIfNotExists()
    {
        if (($recoverPackageId = get_option(self::OPTION_RECOVER_PACKAGE_ID)) === false) {
            return;
        }

        if (in_array($recoverPackageId, array_keys(self::$recoveablesPackages))) {
            return;
        }

        $recoverPackage = DUP_PRO_Package::get_by_id($recoverPackageId);
        if (!$recoverPackage instanceof DUP_PRO_Package) {
            return;
        }

        self::recoverablePackageCheck($recoverPackage);
    }

    public static function isPackageIdRecoveable($id, $reset = false)
    {
        return in_array($id, self::getRecoverablesPackagesIds($reset));
    }

    public static function getRecoverablesPackagesIds($reset = false)
    {
        return array_keys(self::getRecoverablesPackages($reset));
    }

    public static function recoverablePackageCheck(DUP_PRO_Package $package)
    {
        if (version_compare($package->Version, self::IMPORT_ENABLE_MIN_VERSION, '<')) {
            return;
        }

        $archivePath = $package->get_local_package_file(DUP_PRO_Package_File_Type::Archive);
        if (!file_exists($archivePath)) {
            return;
        }

        if ($package->Archive->ExportOnlyDB) {
            return;
        }

        if (!empty($package->Multisite->FilterSites)) {
            return;
        }

        if ($package->Archive->hasWpCoreFolderFiltered()) {
            return;
        }

        if ($package->statusInfo['hasFilteredSiteTables']) {
            return;
        }

        self::$recoveablesPackages[$package->ID] = array(
            'id'       => $package->ID,
            'created'  => $package->Created,
            'nameHash' => $package->NameHash,
            'name'     => $package->Name
        );
    }
}