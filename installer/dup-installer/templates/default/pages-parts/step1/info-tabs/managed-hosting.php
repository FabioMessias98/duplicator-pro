<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$hostManager = DUPX_Custom_Host_Manager::getInstance();
if (($identifier  = $hostManager->isManaged()) === false) {
    return;
}
$hostObj = $hostManager->getHosting($identifier);
?>
<div id="tabs-2">
    <h3><b><?php echo $hostObj->getLabel(); ?></b> managed hosting detected</h3>
    <p>
        The installation is occurring on a WordPress managed host. Managed hosts are more restrictive than standard shared hosts so some installer settings cannot be changed. 
        These settings include new path, new URL, database connection data, and wp-config settings.
    </p>
</div>