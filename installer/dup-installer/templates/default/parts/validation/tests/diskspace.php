<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* Variables */
/* @var $isOk bool */
?>
<p>
    <?php
    if ($isOk) {
        ?><span class="green">You have sufficient disk space in your machine to extract the archive.</span><?php
    } else {
        ?><span class="red">You don’t have sufficient disk space in your machine to extract the archive. Ask your host to increase disk space.</span><?php
    }
    ?>
</p>