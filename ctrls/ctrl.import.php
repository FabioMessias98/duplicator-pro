<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/views/inc.header.php');

class DUP_PRO_CTRL_import
{

    const PAGE_ID             = 'duplicator-pro_page_duplicator-pro-import';
    const PAGE_ID_NETWORK     = 'duplicator-pro_page_duplicator-pro-import-network';
    const USER_META_VIEW_MODE = 'dup-pro-import-view-mode';
    const VIEW_MODE_BASIC     = 'single';
    const VIEW_MODE_ADVANCED  = 'list';

    public static function init()
    {
        add_action('current_screen', array(__CLASS__, 'addHelp'), 99);
    }

    public static function getViewMode()
    {
        if (!($userId = get_current_user_id())) {
            throw new Exception(DUP_PRO_U::esc_html__('User not logged in'));
        }

        if (!($viewMode = get_user_meta($userId, self::USER_META_VIEW_MODE, true))) {
            $viewMode = self::VIEW_MODE_BASIC;
        }
        return $viewMode;
    }

    /**
     * 
     * @param WP_Screen $currentScreen
     * @return boolean
     */
    public static function addHelp($currentScreen)
    {
        if (!self::isImportPage()) {
            return false;
        }
        $currentScreen->add_help_tab(array(
            'id'      => 'dup-pro-help-tab-import',
            'title'   => DUP_PRO_U::esc_html__('Import'),
            'content' => self::getHelpContent()
        ));

        $currentScreen->set_help_sidebar(self::getHelpSidebar());
    }

    protected static function getHelpSidebar()
    {
        ob_start();
        ?>
        <div class="dpro-screen-hlp-info"><b><?php DUP_PRO_U::esc_html_e('Resources'); ?>:</b> 
            <ul>
                <?php echo DUP_PRO_UI_Screen::getHelpSidebarBaseItems(); ?>
                <li>
                    <i class='fas fa-cog'></i> <a href='admin.php?page=duplicator-pro-settings&tab=import'>
                        <?php DUP_PRO_U::esc_html_e('Import Settings'); ?>
                    </a>
                </li>
                <li>
                    <i class='fas fa-mouse-pointer'></i> <a href='https://snapcreek.com/blog/how-migrate-wordpress-site-drag-drop-duplicator-pro/' target='_sc-ddguide'>
                        <?php DUP_PRO_U::esc_html_e('Drag and Drop Guide'); ?>
                    </a>
                </li>                
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * import installer controller 
     * 
     * @throws Exception
     */
    public static function controller()
    {
        self::doView();
    }

    public static function isImportPage()
    {
        if (!($screen = get_current_screen())) {
            return false;
        }
        return ($screen->id == self::PAGE_ID || $screen->id == self::PAGE_ID_NETWORK);
    }

    public static function getImportPageLink()
    {
        if (is_multisite()) {
            $url = network_admin_url('admin.php');
        } else {
            $url = admin_url('admin.php');
        }
        $queryStr = http_build_query(array(
            'page' => 'duplicator-pro-import'
        ));
        return $url.'?'.$queryStr;
    }

    /**
     * 
     * @staticvar int $chunkSize
     * @return int // chunk size in k
     */
    public static function getChunkSize()
    {
        static $chunkSize = null;
        if (is_null($chunkSize)) {
            $postMaxSize       = DupProSnapLibUtil::convertToBytes(ini_get('post_max_size'));
            $uploadMaxFilesize = DupProSnapLibUtil::convertToBytes(ini_get('upload_max_filesize'));
            $chunkSettings     = DupProSnapLibUtil::convertToBytes(DUP_PRO_Global_Entity::get_instance()->import_chunk_size.'k');

            $chunkSize = floor(min(
                    empty($postMaxSize) ? PHP_INT_MAX : max(0, $postMaxSize - (1 * MB_IN_BYTES)),
                    empty($uploadMaxFilesize) ? PHP_INT_MAX : $uploadMaxFilesize,
                    $chunkSettings
                ) / 1024);
        }
        return $chunkSize;
    }

    public static function getChunkSizes()
    {
        return array(
            128   => DUP_PRO_U::__('100k [Slowest]'),
            256   => '200k',
            512   => '500k',
            1024  => '1M',
            2048  => '2M',
            5120  => '5M',
            10240 => DUP_PRO_U::__('10M [Very Fast]'),
            0     => DUP_PRO_U::__('Disabled [Fastest, BUT php.ini limits archive size]'),
        );
    }

    /**
     * parse view for import-installer
     */
    protected static function getHelpContent()
    {
        ob_start();
        require(DUPLICATOR_PRO_PLUGIN_PATH.'/views/tools/import/import-help.php');
        return ob_get_clean();
    }

    /**
     * parse view for import-installer
     */
    protected static function doView()
    {
        $viewMode = self::getViewMode();
        $archives = DUP_PRO_Package_Importer::getArchiveList();
        if ($viewMode == DUP_PRO_CTRL_import::VIEW_MODE_BASIC && count($archives) > 1) {
            $viewMode = DUP_PRO_CTRL_import::VIEW_MODE_ADVANCED;
            update_user_meta(get_current_user_id(), DUP_PRO_CTRL_import::USER_META_VIEW_MODE, $viewMode);

            $adminMessageViewModeSwtich = true;
        } else {
            $adminMessageViewModeSwtich = false;
        }
        require(DUPLICATOR_PRO_PLUGIN_PATH.'/views/tools/import/import.php');
    }
}