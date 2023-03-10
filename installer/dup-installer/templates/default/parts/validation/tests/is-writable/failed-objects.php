<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (empty($failedObjects)) {
    return;
}
?><p>
    <b>Overwrite fails for these folders or files (change permissions or remove then restart):</b>
</p>
<ul class="validation-iswritable-failes-objects red monospace" >
    <?php foreach ($failedObjects as $failedPath) { ?>
        <li>
            <?php echo DUPX_U::esc_html($failedPath); ?> 
        </li>
    <?php } ?>
</ul>