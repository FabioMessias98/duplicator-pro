<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* Variables */
/* @var $isOk bool */
/* @var $extraData array */
/* @var $errorMessage string */

$statusClass = $isOk ? 'green' : 'red';
?>
<div class="sub-title">STATUS</div>
<p class="<?php echo $statusClass; ?>">
    <?php if ($isOk) { ?>
        The current server supports the source site's Character set and Collate (set in the wp-config file).
    <?php } else { ?>
        This server's database does not support the source site's character set and collation pair (set in the wp-config file), so the installer is going to use default character set and collation.
    <?php } ?>
</p>
