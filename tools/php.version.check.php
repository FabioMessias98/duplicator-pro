<?php
/**
 * These functions are performed before including any other Duplicator file so do not use any Duplicator library or feature
 * 
 */
defined('ABSPATH') || exit;

define('DUPLICATOR_PRO_PHP_MINIMUM_VERSION', '5.3.8');
define('DUPLICATOR_PRO_PHP_SUGGESTED_VERSION', '5.6.20');

function duplicator_pro_check_php_version()
{
    if (version_compare(PHP_VERSION, DUPLICATOR_PRO_PHP_MINIMUM_VERSION, '<')) {
        if (is_multisite()) {
            add_action('network_admin_notices', 'duplicator_pro_php_version_notice');
        } else {
            add_action('admin_notices', 'duplicator_pro_php_version_notice');
        }
        return false;
    } else {
        return true;
    }
}

function duplicator_pro_php_version_notice()
{
    ?>
    <div class="error notice">
        <p>
            <?php printf(__('Your system is running a very old version of PHP (%s) that is no longer suppported by Duplicator Pro.', 'duplicator-pro'), PHP_VERSION); ?>
        </p>
        <p>
            <?php printf(__('Please ask your host to update to PHP %1s or greater.  If this is impossible, ', 'duplicator-pro') . '<a href="https://snapcreek.com/ticket" target="blank">' . __('open a ticket', 'duplicator-pro') . '</a>' . __(' to request a previous version of Duplicator Pro compatible with PHP %2s.', 'duplicator-pro'), DUPLICATOR_PRO_PHP_SUGGESTED_VERSION, PHP_VERSION); ?>
        </p>
    </div>
    <?php
}
