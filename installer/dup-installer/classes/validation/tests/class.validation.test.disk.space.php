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

class DUPX_Validation_test_disk_space extends DUPX_Validation_abstract_item
{

    protected function runTest()
    {
        if (!function_exists('disk_free_space')) {
            return self::LV_SKIP;
        }

        $space_free   = @disk_free_space(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW));
        $archive_size = DUPX_Conf_Utils::archiveExists() ? DUPX_Conf_Utils::archiveSize() : 1;

        if ($space_free && $archive_size > 0 && $space_free > $archive_size) {
            return self::LV_GOOD;
        } else {
            return self::LV_SOFT_WARNING;
        }
    }

    public function getTitle()
    {
        return 'Sufficient Disk Space';
    }

    protected function swarnContent()
    {
        return dupxTplRender('parts/validation/tests/diskspace', array(
            'isOk' => false
            ), false);
    }

    protected function goodContent()
    {
        return dupxTplRender('parts/validation/tests/diskspace', array(
            'isOk' => true
            ), false);
    }
}