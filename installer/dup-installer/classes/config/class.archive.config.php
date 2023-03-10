<?php
/**
 * Class used to control values about the package meta data
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\ArchiveConfig
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

abstract class DUPX_LicenseType
{

    const Unlicensed   = 0;
    const Personal     = 1;
    const Freelancer   = 2;
    const BusinessGold = 3;

}

/**
 * singleton class
 */
class DUPX_ArchiveConfig
{

    const NOTICE_ID_PARAM_EMPTY = 'param_empty_to_validate';

    //READ-ONLY: COMPARE VALUES
    public $created;
    public $version_dup;
    public $version_wp;
    public $version_db;
    public $version_php;
    public $version_os;
    public $packInfo;
    public $fileInfo;
    public $dbInfo;
    public $wpInfo;
    //GENERAL
    public $secure_on;
    public $secure_pass;
    public $skipscan;
    public $installer_base_name      = '';
    public $installer_backup_name    = '';
    public $package_name;
    public $package_hash;
    public $package_notes;
    public $wp_tableprefix;
    public $blogname;
    public $wplogin_url;
    public $relative_content_dir;
    public $blogNameSafe;
    public $exportOnlyDB;
    //ADV OPTS
    public $wproot;
    public $opts_delete;
    //MULTISITE
    public $mu_mode;
    public $mu_generation;
    public $subsites                 = array();
    public $main_site_id             = null;
    public $mu_is_filtered;
    public $mu_siteadmins            = array();
    //LICENSING
    public $license_limit;
    //PARAMS
    public $overwriteInstallerParams = array();

    /**
     *
     * @var self 
     */
    private static $instance = null;

    /**
     * Loads a usable object from the archive.txt file found in the dup-installer root
     *
     * @param string $path	// The root path to the location of the server config files
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

    protected function __construct()
    {
        $config_filepath = DUPX_Package::getPackageArchivePath();
        if (file_exists($config_filepath)) {
            $file_contents = file_get_contents($config_filepath);
            $ac_data       = json_decode($file_contents);

            foreach ($ac_data as $key => $value) {
                $this->{$key} = $value;
            }
        } else {
            throw new Exception("$config_filepath doesn't exist");
        }

        //Instance Updates:
        $this->blogNameSafe = preg_replace("/[^A-Za-z0-9?!]/", '', $this->blogname);
    }

    /**
     * Returns the license type this installer file is made of.
     *
     * @return obj	Returns an enum type of DUPX_LicenseType
     */
    public function getLicenseType()
    {
        $license_type = DUPX_LicenseType::Personal;

        if ($this->license_limit < 0) {
            $license_type = DUPX_LicenseType::Unlicensed;
        } else if ($this->license_limit < 15) {
            $license_type = DUPX_LicenseType::Personal;
        } else if ($this->license_limit < 500) {
            $license_type = DUPX_LicenseType::Freelancer;
        } else if ($this->license_limit >= 500) {
            $license_type = DUPX_LicenseType::BusinessGold;
        }

        return $license_type;
    }

    /**
     * 
     * @return bool
     */
    public function isZipArchive()
    {
        $extension = strtolower(pathinfo($this->package_name, PATHINFO_EXTENSION));
        return ($extension == 'zip');
    }

    /**
     * 
     * @param string $define
     * @return bool             // return true if define value exists
     */
    public function defineValueExists($define)
    {
        return isset($this->wpInfo->configs->defines->{$define});
    }

    public function getUsersLists()
    {
        $result = array();
        foreach ($this->wpInfo->adminUsers as $user) {
            $result[$user->id] = $user->user_login;
        }
        return $result;
    }

    /**
     * 
     * @param string $define
     * @param array $default
     * @return array
     */
    public function getDefineArrayValue($define, $default = array(
            'value'      => false,
            'inWpConfig' => false
        ))
    {
        $defines = $this->wpInfo->configs->defines;
        if (isset($defines->{$define})) {
            return (array) $defines->{$define};
        } else {
            return $default;
        }
    }

    /**
     * return define value from archive or default value if don't exists
     * 
     * @param string $define
     * @param mixed $default
     * @return mixed
     */
    public function getDefineValue($define, $default = false)
    {
        $defines = $this->wpInfo->configs->defines;
        if (isset($defines->{$define})) {
            return $defines->{$define}->value;
        } else {
            return $default;
        }
    }

    /**
     * return define value from archive or default value if don't exists in wp-config
     * 
     * @param string $define
     * @param mixed $default
     * @return mixed
     */
    public function getWpConfigDefineValue($define, $default = false)
    {
        $defines = $this->wpInfo->configs->defines;
        if (isset($defines->{$define}) && $defines->{$define}->inWpConfig) {
            return $defines->{$define}->value;
        } else {
            return $default;
        }
    }

    public function inWpConfigDefine($define)
    {
        $defines = $this->wpInfo->configs->defines;
        if (isset($defines->{$define})) {
            return $defines->{$define}->inWpConfig;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param string $key
     * @return boolean
     */
    public function realValueExists($key)
    {
        return isset($this->wpInfo->configs->realValues->{$key});
    }

    /**
     * return read value from archive if exists of default if don't exists
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getRealValue($key, $default = false)
    {
        $values = $this->wpInfo->configs->realValues;
        if (isset($values->{$key})) {
            return $values->{$key};
        } else {
            return $default;
        }
    }

    /**
     * 
     * @return string
     */
    public function getBlognameFromSelectedSubsiteId()
    {
        $subsiteId = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);
        $blogname  = $this->blogname;
        if ($subsiteId > 0) {
            foreach ($this->subsites as $subsite) {
                if ($subsiteId == $subsite->id) {
                    $blogname = $subsite->blogname;
                    break;
                }
            }
        }
        return $blogname;
    }

    /**
     * 
     * @return int
     */
    public function totalArchiveItemsCount()
    {
        return $this->fileInfo->dirCount + $this->fileInfo->fileCount;
    }

    public function isNetwork()
    {
        return $this->mu_mode > 0;
    }

    public function isPartialNetwork()
    {
        return ($this->mu_mode != 0 && count($this->subsites) > 0 && $this->mu_is_filtered);
    }

    public function setNewPathsAndUrlParamsByMainNew()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();

        self::manageEmptyPathAndUrl(DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW, DUPX_Paramas_Manager::PARAM_SITE_URL);
        self::manageEmptyPathAndUrl(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW, DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW);
        self::manageEmptyPathAndUrl(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW, DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW);
        self::manageEmptyPathAndUrl(DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW, DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW);
        self::manageEmptyPathAndUrl(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW, DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW);

        $oldMainPath = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_OLD);
        $newMainPath = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW);

        $newCacheHomeVal = self::getNewSubString($oldMainPath, $newMainPath, $this->getWpConfigDefineValue('WPCACHEHOME'));
        $newVal          = array(
            'value'      => $newCacheHomeVal,
            'inWpConfig' => $this->inWpConfigDefine('WPCACHEHOME') && !empty($newCacheHomeVal)
        );
        $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_WP_CONF_WPCACHEHOME, $newVal);

        $noticeManager = DUPX_NOTICE_MANAGER::getInstance();
        $noticeManager->addNextStepNotice(array(
            'shortMsg'    => '',
            'level'       => DUPX_NOTICE_ITEM::NOTICE,
            'longMsg'     => '<span class="green">If desired, you can change the default values in "Advanced install" &gt; "Other options"</span>.',
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND_IF_EXISTS, self::NOTICE_ID_PARAM_EMPTY);

        $paramsManager->save();
        $noticeManager->saveNotices();
    }

    protected static function manageEmptyPathAndUrl($pathKey, $urlKey)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $validPath     = (strlen($paramsManager->getValue($pathKey)) > 0);
        $validUrl      = (strlen($paramsManager->getValue($urlKey)) > 0);

        if ($validPath && $validUrl) {
            return true;
        }

        $paramsManager->setValue($pathKey, self::getDefaultPathUrlValueFromParamKey($pathKey));
        $paramsManager->setValue($urlKey, self::getDefaultPathUrlValueFromParamKey($urlKey));

        $noticeManager = DUPX_NOTICE_MANAGER::getInstance();
        $msg           = '<b>'.$paramsManager->getLabel($pathKey).' and/or '.$paramsManager->getLabel($urlKey).'</b> can\'t be generated automatically so they are set to their default value.'."<br>\n";
        $msg           .= $paramsManager->getLabel($pathKey).': '.$paramsManager->getValue($pathKey)."<br>\n";
        $msg           .= $paramsManager->getLabel($urlKey).': '.$paramsManager->getValue($urlKey)."<br>\n";

        $noticeManager->addNextStepNotice(array(
            'shortMsg'    => 'URLs and/or PATHs set automatically to their default value.',
            'level'       => DUPX_NOTICE_ITEM::NOTICE,
            'longMsg'     => $msg."<br>\n",
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, self::NOTICE_ID_PARAM_EMPTY);
    }

    public static function getDefaultPathUrlValueFromParamKey($paramKey)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        switch ($paramKey) {
            case DUPX_Paramas_Manager::PARAM_SITE_URL:
                return $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW);
            case DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW:
                return $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW).'/wp-content';
            case DUPX_Paramas_Manager::PARAM_URL_UPLOADS_NEW:
                return $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW).'/uploads';
            case DUPX_Paramas_Manager::PARAM_URL_PLUGINS_NEW:
                return $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW).'/plugins';
            case DUPX_Paramas_Manager::PARAM_URL_MUPLUGINS_NEW:
                return $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_CONTENT_NEW).'/mu-plugins';
            case DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW:
                return $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW);
            case DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW:
                return $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW).'/wp-content';
            case DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW:
                return $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW).'/uploads';
            case DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW:
                return $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW).'/plugins';
            case DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW:
                return $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW).'/mu-plugins';
            default:
                throw new Exception('Invalid URL or PATH param');
        }
    }

    /**
     * 
     * @param string $oldMain
     * @param string $newMain
     * @param string $subOld
     * 
     * @return boolean|string  return false if cant generate new sub string
     */
    public static function getNewSubString($oldMain, $newMain, $subOld)
    {
        if (($relativePath = DupProSnapLibIOU::getRelativePath($subOld, $oldMain)) === false) {
            return false;
        }
        return $newMain.'/'.$relativePath;
    }

    /**
     * 
     * @param string $oldMain
     * @param string $newMain
     * @param string $subOld
     * 
     * @return boolean|string  return false if cant generate new sub string
     */
    public static function getNewSubUrl($oldMain, $newMain, $subOld)
    {

        $parsedOldMain = DupProSnapLibURLU::parseUrl($oldMain);
        $parsedNewMain = DupProSnapLibURLU::parseUrl($newMain);
        $parsedSubOld  = DupProSnapLibURLU::parseUrl($subOld);

        $parsedSubNew           = $parsedSubOld;
        $parsedSubNew['scheme'] = $parsedNewMain['scheme'];
        $parsedSubNew['port']   = $parsedNewMain['port'];

        if ($parsedOldMain['host'] !== $parsedSubOld['host']) {
            return false;
        }
        $parsedSubNew['host'] = $parsedNewMain['host'];

        if (($newPath = self::getNewSubString($parsedOldMain['path'], $parsedNewMain['path'], $parsedSubOld['path'])) === false) {
            return false;
        }
        $parsedSubNew['path'] = $newPath;
        return DupProSnapLibURLU::buildUrl($parsedSubNew);
    }

    /**
     * 
     * @return bool
     */
    public function isTablesCaseSensitive()
    {
        return $this->dbInfo->isTablesUpperCase;
    }

    public function isTablePrefixChanged()
    {
        return $this->wp_tableprefix != DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);
    }

    public function getTableWithNewPrefix($table)
    {
        $search  = '/^'.preg_quote($this->wp_tableprefix, '/').'(.*)/';
        $replace = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX).'$1';
        return preg_replace($search, $replace, $table, 1);
    }

    /**
     * 
     * @param int $subsiteId
     * 
     * @return boolean|string return false if don't exists subsiteid
     */
    public function getSubsitePrefixByParam($subsiteId)
    {
        if (($subSiteObj = $this->getSubsiteObjById($subsiteId)) === false) {
            return false;
        }

        if (!$this->isTablePrefixChanged()) {
            return $subSiteObj->blog_prefix;
        } else {
            $search  = '/^'.preg_quote($this->wp_tableprefix, '/').'(.*)/';
            $replace = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX).'$1';
            return preg_replace($search, $replace, $subSiteObj->blog_prefix, 1);
        }
    }

    public function getMainSiteIndex()
    {
        static $mainSubsiteIndex = null;
        if (is_null($mainSubsiteIndex)) {
            $mainSubsiteIndex = -1;
            if (!empty($this->subsites)) {
                foreach ($this->subsites as $index => $subsite) {
                    if ($subsite->id === $this->main_site_id) {
                        $mainSubsiteIndex = $index;
                        break;
                    }
                }
                if ($mainSubsiteIndex == -1) {
                    $mainSubsiteIndex = 0;
                }
            }
        }
        return $mainSubsiteIndex;
    }

    /**
     * 
     * @staticvar array $subsitesIds
     * @return array
     */
    public function getSubsitesIds()
    {
        static $subsitesIds = null;
        if (is_null($subsitesIds)) {
            $subsitesIds = array();
            foreach ($this->subsites as $subsite) {
                $subsitesIds[] = $subsite->id;
            }
        }

        return $subsitesIds;
    }

    /**
     * 
     * @param int $id
     * @return boolean|stdClass refurn false if id dont exists
     */
    public function getSubsiteObjById($id)
    {
        static $indexCache = array();

        if (!isset($indexCache[$id])) {
            foreach ($this->subsites as $subsite) {
                if ($subsite->id == $id) {
                    $indexCache[$id] = $subsite;
                    break;
                }
            }
            if (!isset($indexCache[$id])) {
                $indexCache[$id] = false;
            }
        }

        return $indexCache[$id];
    }

    public function getOldUrlScheme()
    {
        static $oldScheme = null;
        if (is_null($oldScheme)) {
            $siteUrl   = $this->getRealValue('siteUrl');
            $oldScheme = parse_url($siteUrl, PHP_URL_SCHEME);
            if ($oldScheme === false) {
                $oldScheme = 'http';
            }
        }
        return $oldScheme;
    }

    /**
     * subsite object from archive
     * 
     * @param stdClass $subsite
     * 
     * @return string
     */
    public function getUrlFromSubsiteObj($subsite)
    {
        return $this->getOldUrlScheme().'://'.$subsite->domain.$subsite->path;
    }

    /**
     * 
     * @param stdClass $subsite
     * 
     * @return string
     */
    public function getUploadsUrlFromSubsiteObj($subsite)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $mainUploadUrl = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_UPLOADS_OLD);
        if ($subsite->id == $this->getMainSiteIndex()) {
            return $mainUploadUrl;
        } else {
            $mainOldUrl    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_OLD);
            $subsiteOldUrl = rtrim($this->getUrlFromSubsiteObj($subsite), '/');
            return str_replace($mainOldUrl, $subsiteOldUrl, $mainUploadUrl);
        }
    }

    /**
     * 
     * @return array
     */
    public function getOldUrlsArrayIdVal()
    {
        if (empty($this->subsites)) {
            return array();
        }

        $result = array();

        foreach ($this->subsites as $subsite) {
            $result[$subsite->id] = rtrim($this->getUrlFromSubsiteObj($subsite), '/');
        }
        return $result;
    }

    /**
     * 
     * @return array
     */
    public function getNewUrlsArrayIdVal()
    {
        if (empty($this->subsites)) {
            return array();
        }

        $result        = array();
        $mainSiteIndex = $this->getMainSiteIndex();
        $mainUrl       = $this->getUrlFromSubsiteObj($this->subsites[$mainSiteIndex]);

        foreach ($this->subsites as $subsite) {
            $result[$subsite->id] = DUPX_U::getDefaultURL($this->getUrlFromSubsiteObj($subsite), $mainUrl);
        }

        return $result;
    }

    /**
     * 
     * @return string
     */
    public function getNewCookyeDomainFromOld()
    {
        if ($this->getWpConfigDefineValue('COOKIE_DOMAIN')) {
            $parsedUrlNew = parse_url(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW));
            $parsedUrlOld = parse_url(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_URL_OLD));

            $cookieDomain = $this->getWpConfigDefineValue('COOKIE_DOMAIN', null);
            if ($cookieDomain == $parsedUrlOld['host']) {
                return $parsedUrlNew['host'];
            } else {
                return $cookieDomain;
            }
        } else {
            return false;
        }
    }

    /**
     * get relative path in archive of wordpress main paths
     * 
     * @param string $pathKey (abs,home,plugins ...)
     * @return string
     */
    public function getRelativePathsInArchive($pathKey = null)
    {
        static $realtviePaths = null;

        if (is_null($realtviePaths)) {
            $realtviePaths = (array) $this->getRealValue('archivePaths');
            foreach ($realtviePaths as $key => $path) {
                $realtviePaths[$key] = DupProSnapLibIOU::getRelativePath($path, $this->wpInfo->targetRoot);
            }
        }

        if (!empty($pathKey)) {
            if (array_key_exists($pathKey, $realtviePaths)) {
                return $realtviePaths[$pathKey];
            } else {
                return false;
            }
        } else {
            return $realtviePaths;
        }
    }

    /**
     * 
     * @param string $path
     * @param string|string[] $pathKeys
     * @return boolean
     */
    public function isChildOfArchivePath($path, $pathKeys = array())
    {
        if (is_scalar($pathKeys)) {
            $pathKeys = array($pathKeys);
        }

        $mainPaths = $this->getRelativePathsInArchive();
        foreach ($pathKeys as $key) {
            if (!isset($mainPaths[$key])) {
                continue;
            }

            if (strlen($mainPaths[$key]) == 0) {
                return true;
            }

            if (strpos($path, $mainPaths[$key]) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * 
     * @staticvar string|bool $relativePath return false if PARAM_PATH_MUPLUGINS_NEW isn't a sub path of PARAM_PATH_NEW
     * @return string
     */
    public function getRelativeMuPlugins()
    {
        static $relativePath = null;
        if (is_null($relativePath)) {
            $relativePath = DupProSnapLibIOU::getRelativePath(
                    DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW),
                    DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW)
            );
        }
        return $relativePath;
    }

    /**
     * return the mapping paths from relative path of archive zip and target folder 
     * if exist only one entry return the target folter string
     * 
     * @staticvar string|array $pathsMapping
     * @return string|array
     * 
     * @throws Exception
     */
    public function getPathsMapping()
    {
        static $pathsMapping = null;

        if (is_null($pathsMapping)) {
            $paramsManager = DUPX_Paramas_Manager::getInstance();
            $pathsMapping  = array();

            $targeRootPath = $this->wpInfo->targetRoot;
            $paths         = (array) $this->getRealValue('archivePaths');

            unset($paths['wpconfig']);
            if ($paths['home'] === $paths['abs']) {
                unset($paths['abs']);
            }

            uasort($paths, function ($a, $b) {
                $lenA = count(explode('/', $a));
                $lenB = count(explode('/', $b));
                if ($lenA === $lenB) {
                    return strcmp($a, $b);
                } else if ($lenA > $lenB) {
                    return -1;
                } else {
                    return 1;
                }
            });

            foreach ($paths as $key => $path) {
                if (($relativePath = DupProSnapLibIOU::getRelativePath($path, $targeRootPath)) === false) {
                    $relativePath = $path;
                }

                switch ($key) {
                    case 'home':
                        $newPath = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW);
                        break;
                    case 'abs':
                        $newPath = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_WP_CORE_NEW);
                        break;
                    case 'wpcontent':
                        $newPath = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_CONTENT_NEW);
                        break;
                    case 'uploads':
                        $newPath = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_UPLOADS_NEW);
                        break;
                    case 'plugins':
                        $newPath = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_PLUGINS_NEW);
                        break;
                    case 'muplugins':
                        $newPath = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW);
                        break;
                    case 'themes':
                    default:
                        continue 2;
                }
                $pathsMapping[$relativePath] = $newPath;
            }

            $unsetKeys = array();
            foreach (array_reverse($pathsMapping) as $oldPathA => $newPathA) {
                foreach ($pathsMapping as $oldPathB => $newPathB) {
                    if ($oldPathA == $oldPathB) {
                        continue;
                    }

                    if (($relativePathOld = DupProSnapLibIOU::getRelativePath($oldPathA, $oldPathB)) === false ||
                        ($relativePathNew = DupProSnapLibIOU::getRelativePath($newPathA, $newPathB)) === false) {
                        continue;
                    }

                    if ($relativePathOld == $relativePathNew) {
                        $unsetKeys[] = $oldPathA;
                        break;
                    }
                }
            }
            foreach (array_unique($unsetKeys) as $unsetKey) {
                unset($pathsMapping[$unsetKey]);
            }

            $tempArray    = $pathsMapping;
            $pathsMapping = array();
            foreach ($tempArray as $key => $val) {
                $pathsMapping['/'.$key] = $val;
            }

            switch (count($pathsMapping)) {
                case 0:
                    throw Exception('Paths archive mapping is inconsistent');
                case 1:
                    $pathsMapping = reset($pathsMapping);
                default:
            }
        }
        return $pathsMapping;
    }

    /**
     * get absolute target path from archive relative path 
     * 
     * @param string $archiveFile
     * 
     * @return string
     */
    public function destFileFromArchiveName($archiveFile)
    {
        static $pathsMapping = null;
        if (is_null($pathsMapping)) {
            $pathsMapping = $this->getPathsMapping();
        }

        if (is_string($pathsMapping)) {
            return $pathsMapping.'/'.ltrim($archiveFile, '\\/');
        } else {
            if ($archiveFile[0] != '/') {
                $archiveFile = '/'.$archiveFile;
            }
            foreach ($pathsMapping as $archiveMainPath => $newMainPath) {
                if (($relative = DupProSnapLibIOU::getRelativePath($archiveFile, $archiveMainPath)) !== false) {
                    return $newMainPath.'/'.$relative;
                }
            }

            // if don't find corrispondance in mapping get the path new as default (this should never happen)
            return DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW).'/'.ltrim($archiveFile, '\\/');
        }
    }

    /**
     * 
     * @param DupProWPConfigTransformer $confTrans
     * @param string $defineKey
     * @param string $paramKey
     */
    public static function updateWpConfigByParam($confTrans, $defineKey, $paramKey)
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $wpConfVal     = $paramsManager->getValue($paramKey);
        return self::updateWpConfigByValue($confTrans, $defineKey, $wpConfVal);
    }

    /**
     * 
     * @param DupProWPConfigTransformer $confTrans
     * @param string $defineKey
     * @param mixed $wpConfVal
     */
    public static function updateWpConfigByValue($confTrans, $defineKey, $wpConfVal)
    {
        if ($wpConfVal['inWpConfig']) {
            $stringVal = '';
            switch (gettype($wpConfVal['value'])) {
                case "boolean":
                    $stringVal = $wpConfVal['value'] ? 'true' : 'false';
                    $updParam  = array('raw' => true, 'normalize' => true);
                    break;
                case "integer":
                case "double":
                    $stringVal = (string) $wpConfVal['value'];
                    $updParam  = array('raw' => true, 'normalize' => true);
                    break;
                case "string":
                    $stringVal = $wpConfVal['value'];
                    $updParam  = array('raw' => false, 'normalize' => true);
                    break;
                case "NULL":
                    $stringVal = 'null';
                    $updParam  = array('raw' => true, 'normalize' => true);
                    break;
                case "array":
                case "object":
                case "resource":
                case "resource (closed)":
                case "unknown type":
                default:
                    $stringVal = '';
                    $updParam  = array('raw' => true, 'normalize' => true);
                    brack;
            }
            DUPX_Log::info('WP CONFIG UPDATE '.$defineKey.' '.DUPX_Log::varToString($wpConfVal['value']));
            $confTrans->update('constant', $defineKey, $stringVal, $updParam);
        } else {
            if ($confTrans->exists('constant', $defineKey)) {
                DUPX_Log::info('WP CONFIG REMOVE '.$defineKey);
                $confTrans->remove('constant', $defineKey);
            }
        }
    }
}