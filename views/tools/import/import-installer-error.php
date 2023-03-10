<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* Variables */
/* @var $errorMessage string */
?><div class="wrap">
    <?php duplicator_pro_header(DUP_PRO_U::__("Install package error")); ?>
    <p>
        <?php DUP_PRO_U::esc_html_e("Error on package prepare"); ?><br>
        <?php
        DUP_PRO_U::esc_html_e('Message: ');
        echo esc_html($errorMessage); ?>
    </p>
</div>