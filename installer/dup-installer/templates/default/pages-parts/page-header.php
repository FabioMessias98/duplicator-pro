<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?><!DOCTYPE html>
<html>
    <head>
        <?php dupxTplRender('pages-parts/head/meta'); ?>
        <title>Duplicator Professional</title>
        <?php dupxTplRender('pages-parts/head/css-scripts'); ?>
        <?php dupxTplRender('pages-parts/head/css-template-custom'); ?>
    </head>
    <body id="<?php echo $bodyId; ?>" class="<?php echo $bodyClasses; ?>" >
        <div id="content">
            <?php
            dupxTplRender('parts/top-header.php', array(
                'paramView' => $paramView
            ));
            if (!isset($skipTopMessages) || $skipTopMessages !== true) {
                dupxTplRender('parts/top-messages.php');
            }
