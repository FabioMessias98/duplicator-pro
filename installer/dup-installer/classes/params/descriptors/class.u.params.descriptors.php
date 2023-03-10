<?php
/**
 * Installer params manager
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPX_INIT.'/classes/params/descriptors/interface.params.descriptor.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.controller.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.url.path.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.gen.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.database.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.cpanel.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.replace.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.plugins.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.users.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.new.admin.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.wpconfig.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.validation.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.engines.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.configs.php');
require_once(DUPX_INIT.'/classes/params/descriptors/class.params.descriptor.multisite.php');

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class DUPX_Paramas_Descriptors implements DUPX_Interface_Paramas_Descriptor
{

    /**
     * 
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        DUPX_Paramas_Descriptor_urls_paths::init($params);
        DUPX_Paramas_Descriptor_controller::init($params);
        DUPX_Paramas_Descriptor_generic::init($params);
        DUPX_Paramas_Descriptor_engines::init($params);
        DUPX_Paramas_Descriptor_configs::init($params);
        DUPX_Paramas_Descriptor_validation::init($params);
        DUPX_Paramas_Descriptor_database::init($params);
        DUPX_Paramas_Descriptor_cpanel::init($params);
        DUPX_Paramas_Descriptor_replace::init($params);
        DUPX_Paramas_Descriptor_multisite::init($params);
        DUPX_Paramas_Descriptor_plugins::init($params);
        DUPX_Paramas_Descriptor_users::init($params);
        DUPX_Paramas_Descriptor_new_admin::init($params);
        DUPX_Paramas_Descriptor_wpconfig::init($params);
    }

    /**
     * 
     * @param DUPX_Param_item[] $params
     */
    public static function updateParamsAfterOverwrite(&$params)
    {
        DUPX_LOG::info('UPDATE PARAMS AFTER OVERWRITE', DUPX_Log::LV_DETAILED);

        DUPX_Paramas_Descriptor_urls_paths::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_controller::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_generic::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_engines::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_configs::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_validation::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_database::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_cpanel::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_replace::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_multisite::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_plugins::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_users::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_new_admin::updateParamsAfterOverwrite($params);
        DUPX_Paramas_Descriptor_wpconfig::updateParamsAfterOverwrite($params);
    }

    public static function validateNotEmpty($value)
    {
        if (is_string($value)) {
            return strlen($value) > 0;
        } else {
            return !empty($value);
        }
    }

    /**
     * sanitize path
     *
     * @param string $value
     * @return string
     */
    public static function sanitizePath($value)
    {
        $result = DupProSnapLibUtil::sanitize_non_stamp_chars_newline_and_trim($value);
        return DupProSnapLibIou::safePathUntrailingslashit($result);
    }

    /**
     * the path can't be empty
     *
     * @param string $value
     * @return bool
     */
    public static function validatePath($value)
    {
        return strlen($value) > 1;
    }

    /**
     * sanitize URL
     *
     * @param string $value
     * @return string
     */
    public static function sanitizeUrl($value)
    {
        $result = DupProSnapLibUtil::sanitize_non_stamp_chars_newline_and_trim($value);
        if (empty($value)) {
            return '';
        }
        // if scheme not set add http by default
        if (!preg_match('/^[a-zA-Z]+\:\/\//', $result)) {
            $result = 'http://'.ltrim($result, '/');
        }
        return rtrim($result, '/\\');
    }

    /**
     * the url can't be empty
     *
     * @param string $value
     * @return bool
     */
    public static function validateUrlWithScheme($value)
    {
        if (empty($value)) {
            return false;
        }
        if (($parsed = parse_url($value)) === false) {
            return false;
        }
        if (!isset($parsed['host']) || empty($parsed['host'])) {
            return false;
        }
        return true;
    }
}