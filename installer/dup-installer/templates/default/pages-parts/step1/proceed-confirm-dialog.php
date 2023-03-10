<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<div id="db-install-dialog-confirm" title="Install Confirmation" style="display:none">
    <div style="padding: 10px 0 25px 0">
        <b>Run installer with these settings?</b>
    </div>
    <b>Site settings:</b><br>
    <table style="margin-left:20px">
        <tr>
            <td><b>New URL:</b></td>
            <td><i id="dlg-url-new"></i></td>
        </tr>
        <tr>
            <td><b>New Path:</b></td>
            <td><i id="dlg-path-new"></i></td>
        </tr>
    </table>
    <p></p>  
    
    <b>Database Settings:</b><br/>
    <table style="margin-left:20px">
        <tr>
            <td><b>Server:</b></td>
            <td><i id="dlg-dbhost"></i></td>
        </tr>
        <tr>
            <td><b>Name:</b></td>
            <td><i id="dlg-dbname"></i></td>
        </tr>
        <tr>
            <td><b>User:</b></td>
            <td><i id="dlg-dbuser"></i></td>
        </tr>
    </table>
    <p></p>    

    <small>
        <i class="fa fa-exclamation-triangle"></i> WARNING: Be sure these database parameters are correct! Entering the wrong information WILL overwrite an existing database.
        Make sure to have backups of all your data before proceeding.
    </small>
</div>