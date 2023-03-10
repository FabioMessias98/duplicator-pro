<?php
/**
 * godaddy custom hosting class
 *
 * Standard: PSR-2
 *
 * @package SC\DUPX\DB
 * @link http://www.php-fig.org/psr/psr-2/
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * class for wordpress.com managed hosting
 * 
 * @todo not yet implemneted
 * 
 */
class DUPX_WordpressCom_Host implements DUPX_Host_interface
{

    /**
     * return the current host itentifier
     *
     * @return string
     */
    public static function getIdentifier()
    {
        return DUPX_Custom_Host_Manager::HOST_WORDPRESSCOM;
    }

    /**
     * @return bool true if is current host
     */
    public function isHosting()
    {
        // check only mu plugin file exists
        
        $testFile = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW).'/wpcomsh-loader.php';
        return file_exists($testFile);
    }

    /**
     * the init function.
     * is called only if isHosting is true
     *
     * @return void
     */
    public function init()
    {
        
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return 'Wordpress.com';
    }

    /**
     * this function is called if current hosting is this
     */
    public function setCustomParams()
    {
        $paramsManager = DUPX_Paramas_Manager::getInstance();
        $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_ARCHIVE_ACTION, DUP_PRO_Extraction::ACTION_SKIP_CORE_FILES);

        $overwriteData = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_OVERWRITE_SITE_DATA);
        if (!DUPX_InstallerState::isRecoveryMode() && !empty($overwriteData['adminUsers'])) {
            $keepUserId = PHP_INT_MAX;

            foreach ($overwriteData['adminUsers'] as $user) {
                if ($keepUserId > $user['id']) {
                    $keepUserId = $user['id'];
                }
            }

            DUPX_Log::info('WORDPRESS.COM DETECT SET KEEP USERS ON USER '.$keepUserId);
            $paramsManager->setValue(DUPX_Paramas_Manager::PARAM_KEEP_TARGET_SITE_USERS, $keepUserId);
        }
    }
}