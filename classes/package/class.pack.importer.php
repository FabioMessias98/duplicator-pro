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
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUP_PRO_Package_Importer
{

    const IMPORT_ENABLE_MIN_VERSION = '4.0.0-alpha2';

    /**
     *
     * @var string  
     */
    protected $archive = null;

    /**
     *
     * @var string 
     */
    protected $ext = null;

    /**
     *
     * @var bool 
     */
    protected $isValid = false;

    /**
     *
     * @var string 
     */
    protected $notValidMessage = '';

    /**
     *
     * @var object
     */
    protected $info = null;

    /**
     * @var object
     */
    protected $scan = null;

    /**
     *
     * @var string
     */
    protected $nameHash = null;

    /**
     *
     * @var string 
     */
    protected $hash = null;

    /**
     * 
     * @param scrinf $path // valid archive patch
     * @throws Exception if file ins't valid
     */
    public function __construct($path)
    {
        if (!is_file($path)) {
            throw new Exception('Archive path "'.$path.'" is invalid');
        }

        DupProSnapLibIOU::chmod($path, 'u+rw');
        if (!is_readable($path)) {
            throw new Exception('Can\'t read the archive "'.$path.'"');
        }

        if (!preg_match(DUPLICATOR_PRO_ARCHIVE_REGEX_PATTERN, basename($path))) {
            throw new Exception('Invalid archive name "'.$path.'"');
        }

        $this->archive  = $path;
        $this->ext      = pathinfo($this->archive, PATHINFO_EXTENSION);
        $this->hash     = self::getHashFromArchiveName($this->archive);
        $this->nameHash = self::getNameHashFromArchiveName($this->archive);

        $this->initInfoObjects();
    }

    /**
     * This function extract a single file from archive and put it in target folder.
     * If the file is in a subfolder it is put in a subfolder in the target folder
     * Es. file = subfolder/file.txt is extracted in /target/folder/subsolder/file.txt
     * 
     * @param string $file
     * @param string $targetFolder
     * @return string   // extracted file fullpath
     * 
     * @throws Exception if the extraction fails
     */
    protected function extractSingleFile($file, $targetFolder)
    {
        
        $targetFile = DupProSnapLibIOU::trailingslashit($targetFolder).$file;

        switch ($this->ext) {
            case 'zip':
                if (!class_exists('ZipArchive', false)) {
                    throw new Exception('ZipArchive PHP module doesn\'t exist so current package can\'t be opened');
                }

                $zip = new ZipArchive;
                if ($zip->open($this->archive) !== true) {
                    throw new Exception('Can\'t open ZipArcive '.$this->archive);
                }
                if (($fileContent = $zip->getFromName($file)) === false) {
                    $zip->close();
                    throw new Exception('Can\'t get file '.$file.' from archive '.$this->archive);
                }
                $zip->close();

                if (DupProSnapLibIOU::mkdir_p(dirname($targetFile)) === false) {
                    throw new Exception('Can\'t create file content folder '.dirname($targetFile));
                }

                if (file_put_contents($targetFile, $fileContent) === false) {
                    throw new Exception('Can\'t create file '.$targetFile);
                }
                break;
            case 'daf':
                DupArchiveEngine::expandFiles($this->archive, array($file), $targetFolder);
                break;
            default:
                throw new Exception('Invalid archive extension "'.$this->ext.'"');
        }

        if (!file_exists($targetFile)) {
            throw new Exception('Can\'t extract file '.$targetFile.' from archive');
        }

        return $targetFile;
    }

    /**
     * this function extract archive info package and read it, After initializing the information deletes the file.
     *
     * @throws Exception // throw exception if can't read info
     */
    protected function initInfoObjects()
    {
        try {
            $this->info    = $this->getObjectFromJson('dup-installer/dup-archive__'.$this->hash.'.txt');
            $this->scan    = $this->getObjectFromJson('dup-installer/dup-scan__'.$this->hash.'.json');
            $this->isValid = true;
        }
        catch (Exception $ex) {
            DUP_PRO_Log::trace("Couldn't initialize the info object: ".$ex->getMessage());
            $this->notValidMessage = $ex->getMessage();
            $this->isValid         = false;
        }
    }

    /**
     * @param string $relativePathInArchive Relative path to the file contains the json in the archive
     * @return object The decoded json object
     * @throws Exception
     */
    protected function getObjectFromJson($relativePathInArchive)
    {
        $tempArchiveJson = $this->extractSingleFile($relativePathInArchive, DUPLICATOR_PRO_SSDIR_PATH_TMP_IMPORT);

        if (($jsonContent = file_get_contents($tempArchiveJson)) === false) {
            throw new Exception('Can\'t read tmp json file '.$relativePathInArchive);
        }
        unlink($tempArchiveJson);

        if (($result = json_decode($jsonContent)) === false) {
            throw new Exception('Can\'t decode scan json '.$relativePathInArchive);
        }

        return $result;
    }

    /**
     * return admin installer page ling with right query string
     * 
     * @return string
     */
    public function getInstallerPageLink()
    {
        if (is_multisite()) {
            $url = network_admin_url('admin.php');
        } else {
            $url = admin_url('admin.php');
        }

        $queryStr = http_build_query(array(
            'page'    => DUP_PRO_Constants::$IMPORT_INSTALLER_PAGE,
            'package' => $this->archive
        ));
        return $url.'?'.$queryStr;
    }

    /**
     * 
     * @param bool $removeArchive if true remove all or exclude archives
     * @return bool
     */
    public static function cleanImportFolder($removeArchive = false)
    {
        $regex         = $removeArchive ? '/^$/' : DUPLICATOR_PRO_ARCHIVE_REGEX_PATTERN;
        $filesToRemove = DupProSnapLibIOU::regexGlob(DUPLICATOR_PRO_PATH_IMPORTS, $regex, false, true);

        foreach ($filesToRemove as $file) {
            DupProSnapLibIOU::rrmdir($file);
        }

        return true;
    }

    /**
     * This function prepares the installer execution by extracting the installer-backup.php file and creating the overwrite parameter file
     * 
     * @return string // installer.php link with right params.
     * @throws Exception
     */
    public function prepareToInstall()
    {
        $failMessage = '';
        self::cleanImportFolder();
        if (!$this->isImportable($failMessage)) {
            throw new Exception($failMessage);
        }

        $this->createOverwriteParams();
        $installerLink = $this->extractInstallerBackup();

        return $installerLink;
    }

    protected function getInstallerFolderPath()
    {
        return DUPLICATOR_PRO_PATH_IMPORTS;
    }

    protected function getInstallerFolderUrl()
    {
        return DUPLICATOR_PRO_URL_IMPORTS;
    }

    protected function getInstallerName()
    {
        return $this->nameHash.'_'.DUP_PRO_Global_Entity::get_instance()->get_installer_backup_filename();
    }

    /**
     * extract installer-backup.php file in import folder
     *
     * @return string // return installer import URL
     * @throws Exception
     */
    protected function extractInstallerBackup()
    {
        $this->extractSingleFile($this->getInstallerName(), $this->getInstallerFolderPath());
        return $this->getInstallLink();
    }

    public function getInstallLink()
    {
        $queryStr = http_build_query(array(
            'archive'    => $this->archive,
            'dup_folder' => 'dup-installer-'.$this->info->packInfo->secondaryHash
        ));
        return $this->getInstallerFolderUrl().'/'.$this->getInstallerName().'?'.$queryStr;
    }

    protected function getOverwriteParams()
    {
        global $wpdb;

        if (DUP_PRO_Package_Recover::getRecoverPackageId() !== false) {
            $recoverPackage     = DUP_PRO_Package_Recover::getRecoverPackage();
            $recoverLink        = $recoverPackage->getInstallLink();
            $packageIsOutToDate = $recoverPackage->isOutToDate();
            $packageLife        = $recoverPackage->getPackageLife();
        } else {
            $recoverLink        = '';
            $packageIsOutToDate = true;
            $packageLife        = 0;
        }


        $updDirs = wp_upload_dir();
        $params  = array(
            /* 'debug_params'        => array(
              'value' => true
              ), */
            'template'            => array(
                'value' => 'import-base',
            ),
            'valid-act'           => array(
                'value' => 'auto',
            ),
            'recovery-link'       => array(
                'value' => $recoverLink,
            ),
            'import-info'         => array(
                'value' => array(
                    'import_page'             => DUP_PRO_CTRL_import::getImportPageLink(),
                    'recovery_page'           => DUP_PRO_CTRL_recovery::getRecoverPageLink(),
                    'recovery_is_out_to_date' => $packageIsOutToDate,
                    'recovery_package_life'   => $packageLife,
                    'color-scheme'            => DUP_PRO_UI_Screen::getCurrentColorScheme(),
                    'color-primary-button'    => DUP_PRO_UI_Screen::getPrimaryButtonColorByScheme()
                )
            ),
            'db-display-overwarn' => array(
                'value' => false,
            ),
            'cpnl-can-sel'        => array(
                'value' => false,
            ),
            'url_new'             => array(
                'value'      => home_url(),
                'formStatus' => 'st_infoonly'
            ),
            'path_new'            => array(
                'value'      => DUP_PRO_Archive::getOriginalPaths('home'),
                'formStatus' => 'st_infoonly'
            ),
            'archive_action'      => array(
                'formStatus' => 'st_infoonly'
            ),
            'archive_engine'      => array(
                'formStatus' => 'st_infoonly'
            ),
            'dbaction'            => array(
                'value'      => 'empty',
                'formStatus' => 'st_infoonly'
            ),
            'dbhost'              => array(
                'value'      => DB_HOST,
                'formStatus' => 'st_infoonly'
            ),
            'dbname'              => array(
                'value'      => DB_NAME,
                'formStatus' => 'st_infoonly'
            ),
            'dbuser'              => array(
                'value'      => DB_USER,
                'formStatus' => 'st_infoonly'
            ),
            'dbpass'              => array(
                'value'      => DB_PASSWORD,
                'formStatus' => 'st_infoonly'
            ),
            'dbcharset'           => array(
                'value'      => DB_CHARSET,
                'formStatus' => 'st_infoonly'
            ),
            'dbcollate'           => array(
                'value'      => DB_COLLATE,
                'formStatus' => 'st_infoonly'
            ),
            'ovr_site_data'       => array(
                'value' => array(
                    'dbhost'       => DB_HOST,
                    'dbname'       => DB_NAME,
                    'dbuser'       => DB_USER,
                    'dbpass'       => DB_PASSWORD,
                    'table_prefix' => $wpdb->base_prefix,
                    'isMultisite'  => is_multisite(),
                    'adminUsers'   => array()
                )
            )
        );

        // if is manage hosting overwrite url and paths
        if (DUP_PRO_Custom_Host_Manager::getInstance()->isManaged()) {
            $urlPathParams = array(
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

            $params = array_merge($params, $urlPathParams);
        }
        return $params;
    }

    /**
     * This function creates the parameter overwriting file
     *
     * @return boolean // return true on success
     *
     * @throws Exception if fail
     */
    protected function createOverwriteParams()
    {
        $overwriteFile = $this->getInstallerFolderPath().'/'.DUPLICATOR_PRO_LOCAL_OVERWRITE_PARAMS.'_'.$this->hash.'.json';

        $params = $this->getOverwriteParams();

        if (file_put_contents($overwriteFile, DupProSnapJsonU::wp_json_encode_pprint($params)) === false) {
            throw new Exception('Can\'t create overwrite param file');
        }

        return true;
    }

    /**
     * this function check if package is importable 
     * 
     * @param string $failMessage
     * @return boolean
     */
    public function isImportable(&$failMessage = null)
    {
        if (!$this->isValid) {
            $failMessage = DUP_PRO_U::__('The package information can\'t be read.')."<br>\n";
            $failMessage .= sprintf(DUP_PRO_U::__('Error: %s'), $this->notValidMessage);
            return false;
        }

        if (version_compare($this->getDupVersion(), self::IMPORT_ENABLE_MIN_VERSION, '<')) {
            $failMessage = sprintf(DUP_PRO_U::__('Package version too old, %s version or higher is required. '.
            'If you want install this package then please use the "classic installer.php" overwrite method explained '.
            '<a target="_blank" href="https://youtu.be/R6oQIH1S6Qw?t=629">here</a>.'), self::IMPORT_ENABLE_MIN_VERSION);
            return false;
        }

        if ($this->info->exportOnlyDB) {
            $failMessage = DUP_PRO_U::__('Database only packages aren\'t supported');
            return false;
        }

        if ($this->scan->ARC->Status->HasFilteredCoreFolders) {
            $failMessage = DUP_PRO_U::__('The package is missing WordPress core folder(s)! It must include wp-admin, wp-content, wp-includes, uploads, plugins, and themes folders.');
            return false;
        }

        if ($this->scan->ARC->Status->HasFilteredSiteTables) {
            $failMessage = DUP_PRO_U::__('The package is missing some of the site tables.');
            return false;
        }

        if (!$this->isCompleteSitePackage()) {
            $failMessage = DUP_PRO_U::__('The package must include all WordPress core directories and all tables present in the source site.');
            return false;
        }

        if (!$this->packageHasRequiredInstallerFiles()) {
            $failMessage = DUP_PRO_U::__('The package lacks some of the installer files.');
            return false;
        }

        $failMessage = '';
        return true;
    }
    /*
     * Checks if no required tables or dirs were filtered during the package build.
     * It does not check if those are then actually present in the archive
     */

    private function isCompleteSitePackage()
    {
        try {
            if (!$this->isValid) {
                throw new Exception("Can't do this check on an invalid package.");
            }

            //check for any filtered tables
            if (!DUP_PRO_Import_U::hasAllRequiredTables($this->info->mu_mode, $this->info->mu_is_filtered, $this->info->dbInfo)) {
                throw new Exception("All tables of the source site have to be included in the package.");
            }
        }
        catch (Exception $ex) {
            DUP_PRO_Log::trace($ex->getMessage());
            return false;
        }

        return true;
    }

    private function packageHasRequiredInstallerFiles()
    {
        try {
            if (!$this->isValid) {
                throw new Exception("Can't do this check on an invalid package.");
            }

            $requiredFilePaths = array(
                self::getNameHashFromArchiveName($this->archive)."_installer-backup.php",
                'dup-installer/main.installer.php',
            );

            foreach ($requiredFilePaths as $path) {
                $tempFile = $this->extractSingleFile($path, DUPLICATOR_PRO_SSDIR_PATH_TMP_IMPORT);

                if (file_get_contents($tempFile) === false) {
                    throw new Exception('Can\'t read installer file: '.$path);
                }
                unlink($tempFile);
            }

            return true;
        }
        catch (Exception $ex) {
            DUP_PRO_Log::trace($ex->getMessage());
        }

        return false;
    }

    /**
     * true if package is valid
     * 
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * return archive full path
     * 
     * @return string
     */
    public function getFullPath()
    {
        return $this->archive;
    }

    /**
     * return archive name
     * 
     * @return string
     */
    public function getName()
    {
        return basename($this->archive);
    }

    /**
     * 
     * @return string
     */
    public function getPackageId()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->packInfo->packageId;
    }

    /**
     * 
     * @return string
     */
    public function getPackageName()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->packInfo->packageName;
    }

    /**
     * return package creation date
     * 
     * @return string
     */
    public function getCreated()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->created;
    }

    /**
     * return archive size
     * 
     * @return int
     */
    public function getSize()
    {
        return filesize($this->archive);
    }

    /**
     * return package version 
     * 
     * @return string
     */
    public function getDupVersion()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->version_dup;
    }

    /**
     * return source site wordpress version
     * 
     * @return string   
     */
    public function getWPVersion()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->version_wp;
    }

    /**
     * return source site PHP version
     * 
     * @return string
     */
    public function getPhpVersion()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->version_php;
    }

    /**
     * return source site home url
     * 
     * @return string
     */
    public function getHomeUrl()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->wpInfo->configs->realValues->homeUrl;
    }

    /**
     * return source site home path
     * 
     * @return string
     */
    public function getHomePath()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->wpInfo->configs->realValues->originalPaths->home;
    }

    /**
     * return source site abs path
     *
     * @return string
     */
    public function getAbsPath()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->wpInfo->configs->realValues->archivePaths->abs;
    }

    /**
     * return package num folders
     * 
     * @return int
     */
    public function getNumFolders()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->fileInfo->dirCount;
    }

    /**
     * return package num files
     * 
     * @return int
     */
    public function getNumFiles()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->fileInfo->fileCount;
    }

    /**
     * return package database size
     * 
     * @return int
     */
    public function getDbSize()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->dbInfo->tablesSizeOnDisk;
    }

    /**
     * return package num tables
     * 
     * @return int
     */
    public function getNumTables()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->dbInfo->tablesFinalCount;
    }

    /**
     * return package num rows
     * 
     * @return int
     */
    public function getNumRows()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->dbInfo->tablesRowCount;
    }

    /**
     * thing function generate html package details 
     * 
     * @param bool $echo
     * 
     * @return string|void
     */
    public function getHtmlDetails($echo = true)
    {
        ob_start();
        $importObj = $this;
        require DUPLICATOR_PRO_PLUGIN_PATH.'/views/tools/import/import-package-details.php';
        if ($echo) {
            ob_end_flush();
        } else {
            return ob_get_clean();
        }
    }

    /**
     * get the list folder to check package to import
     * 
     * @return string[]
     */
    protected static function getFoldersToCheck()
    {
        $result = array();
        if (is_readable(DUPLICATOR_PRO_PATH_IMPORTS) && is_dir(DUPLICATOR_PRO_PATH_IMPORTS)) {
            $result[] = DUPLICATOR_PRO_PATH_IMPORTS;
        }
        /**
         * it has been decided that it is better not to scan the home to look for importable packages, I keep the code for future use
         * 
         * $home = duplicator_pro_get_home_path();
          if (is_readable($home) && is_dir($home)) {
          $result[] = $home;
          } */
        return $result;
    }

    /**
     * get list of all packages avaibale to import sorted by filetime
     * 
     * @return string[]
     */
    public static function getArchiveList()
    {
        $result = array();
        foreach (self::getFoldersToCheck() as $folder) {
            $result = array_merge($result, DupProSnapLibIOU::regexGlob($folder, DUPLICATOR_PRO_ARCHIVE_REGEX_PATTERN));
        }

        usort($result, array(__CLASS__, 'archiveListSort'));
        return $result;
    }

    /**
     * 
     * @param string $a // path
     * @param string $b // path
     * @return int
     */
    public static function archiveListSort($a, $b)
    {
        $timeA = 0;
        $timeB = 0;

        if (file_exists($a)) {
            $timeA = filemtime($a);
        }


        if (file_exists($b)) {
            $timeB = filemtime($b);
        }

        if ($timeA === $timeB) {
            return 0;
        } else if ($timeA > $timeB) {
            return -1;
        } else {
            return 1;
        }
    }

    /**
     * get import objects of all packages avaibles to import 
     * 
     * @return \DUP_PRO_Package_Importer[]
     */
    public static function getArchiveObjects()
    {
        $objects = array();
        foreach (DUP_PRO_Package_Importer::getArchiveList() as $archivePath) {
            try {
                $objects[] = new DUP_PRO_Package_Importer($archivePath);
            }
            catch (Exception $e) {
                DUP_PRO_Log::traceObject('Can\'t read package and continue', $e);
            }
        }

        return $objects;
    }

    /**
     * Get package hash from archive file name
     * 
     * @param $path archive file name
     * @return package hash
     */
    public static function getHashFromArchiveName($path)
    {
        return preg_replace('/^.+_([a-z0-9]{7})[a-z0-9]+_[0-9]{6}([0-9]{8})_archive\.(?:zip|daf)$/', '$1-$2', basename($path));
    }

    /**
     * get package name hash from archive file name
     * 
     * @param string $path
     * @return string 
     */
    public static function getNameHashFromArchiveName($path)
    {
        return preg_replace('/^(.+_[a-z0-9]{7}[a-z0-9]+_[0-9]{6}[0-9]{8})_archive\.(?:zip|daf)$/', '$1', basename($path));
    }
}