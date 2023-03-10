<?php
/**
 * controller step 0
 * 
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

final class DUPX_Ctrl_S0
{

    public static function stepHeaderLog()
    {
        $archive_path  = DUPX_Security::getInstance()->getArchivePath();
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();
        $labelPadSize  = 20;

        DUPX_Log::info("INSTALLER INFO");
        DUPX_Log::info(str_pad('TEMPLATE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_TEMPLATE)));
        DUPX_Log::info(str_pad('VALIDATE ON START', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_VALIDATION_ACTION_ON_START)));
        DUPX_Log::info(str_pad('PATH_NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW)));
        DUPX_Log::info(str_pad('URL_NEW', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($paramsManager->getValue(DUPX_Paramas_Manager::PARAM_URL_NEW)));
        DUPX_Log::info("********************************************************************************");

        $log = "\n--------------------------------------\n";
        $log .= "ARCHIVE INFO\n";
        $log .= "--------------------------------------\n";
        $log .= str_pad('ARCHIVE NAME', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_Log::varToString($archive_path)."\n";
        $log .= str_pad('ARCHIVE SIZE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_U::readableByteSize(DUPX_Conf_Utils::archiveSize())."\n";
        $log .= str_pad('CREATED', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->created."\n";
        $log .= str_pad('WP VERSION', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->version_wp."\n";
        $log .= str_pad('DUP VERSION', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->version_dup."\n";
        $log .= str_pad('DB VERSION', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->version_db."\n";
        $log .= str_pad('DB FILE SIZE', $labelPadSize, '_', STR_PAD_RIGHT).': '.DUPX_U::readableByteSize($archiveConfig->dbInfo->tablesSizeOnDisk)."\n";
        $log .= str_pad('DB TABLES', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->dbInfo->tablesFinalCount."\n";
        $log .= str_pad('DB ROWS', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->dbInfo->tablesRowCount."\n";
        $log .= str_pad('URL HOME', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->getRealValue('homeUrl')."\n";
        $log .= str_pad('URL CORE', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->getRealValue('siteUrl')."\n";
        $log .= str_pad('URL CONTENT', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->getRealValue('contentUrl')."\n";
        $log .= str_pad('URL UPLOAD', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->getRealValue('uploadBaseUrl')."\n";
        $log .= str_pad('URL PLUGINS', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->getRealValue('pluginsUrl')."\n";
        $log .= str_pad('URL MU PLUGINS', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->getRealValue('mupluginsUrl')."\n";
        $log .= str_pad('URL THEMES', $labelPadSize, '_', STR_PAD_RIGHT).': '.$archiveConfig->getRealValue('themesUrl')."\n";

        $paths = (array) $archiveConfig->getRealValue('archivePaths');
        foreach ($paths as $key => $value) {
            $log .= str_pad('PATH '.strtoupper($key), $labelPadSize, '_', STR_PAD_RIGHT).': '.$value."\n";
        }

        if (count($archiveConfig->subsites) > 0) {
            $log .= '***  SUBSITES ***'."\n";
            foreach ($archiveConfig->subsites as $subsite) {
                $log .= 'SUBSITE [ID:'.str_pad($subsite->id, 4, ' ', STR_PAD_LEFT).'] '.DUPX_Log::varToString($subsite->domain.$subsite->path)."\n";
            }
        }

        $plugins = (array) $archiveConfig->wpInfo->plugins;
        $log     .= '***  PLUGINS ***'."\n";
        foreach ($plugins as $plugin) {
            $log .= 'PLUGIN [SLUG:'.str_pad($plugin->slug, 50, ' ', STR_PAD_RIGHT).'][ON:'.str_pad(DUPX_Log::varToString($plugin->active), 5, ' ', STR_PAD_RIGHT).'] '.$plugin->name."\n";
        }

        $log .= "--------------------------------------\n";
        $log .= "--------------------------------------\n";

        DUPX_Log::info($log, DUPX_Log::LV_DEFAULT);
        DUPX_Log::flush();
    }
}