<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

?><table>
    <tr>
        <td>
            <b>Deployment Path:</b>
        </td>
        <td>
            <i><?php echo DUPX_U::esc_html(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW)); ?></i>
        </td>
    </tr>
    <tr>
        <td>
            <b>Suhosin Extension:</b>
        </td>
        <td>    
            <?php
            if (!extension_loaded('suhosin')) {
                ?><i class='green'>Disabled</i><?php
            } else {
                ?><i class='red'>Enabled</i><?php
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <b>PHP Safe Mode:</b>
        </td>
        <td>
            <?php
            if (!DUPX_Server::phpSafeModeOn()) {
                ?><i class='green'>Disabled</i><?php
            } else {
                ?><i class='red'>Enabled</i><?php
            }
            ?>
        </td>
    </tr>
</table>