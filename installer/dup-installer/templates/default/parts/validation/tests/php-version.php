<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* Variables */
/* @var $fromPhp string */
/* @var $toPhp string */
/* @var $isOk bool */
?><p class="<?php echo $isOk ? 'green' : 'red'; ?>" >
    <b style=''>You are migrating site from PHP <?php echo $fromPhp; ?> to PHP <?php echo $toPhp; ?></b>
</p>
<p>
    If the PHP version of your website is different than the PHP version of your package 
    it MAY cause problems with the functioning of your website.
</p>