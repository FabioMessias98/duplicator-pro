<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
$subsite_id    = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);
$safe_mode     = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SAFE_MODE);
?>
<div class="flex-final-button-wrapper" >
    <div class="button-wrapper" >
        <button type="button" id="s4-final-btn" class="default-btn" onclick="DUPX.getAdminLogin()"><i class="fab fa-wordpress"></i> Admin Login</button>
    </div>
    <div class="content-wrapper" >
        Click the Admin Login button to login and finalize this install.<br />
        <input type="checkbox" name="auto-delete" id="auto-delete" checked="true" />
        <label for="auto-delete">Auto delete installer files after login to secure site <small>(recommended!)</small></label>

        <!-- WARN: MU MESSAGES -->
        <div class="s4-warn margin-top-2" style="display:<?php echo ($subsite_id > 0 ? 'block' : 'none') ?>">
            <b>Multisite</b><br />
            Some plugins may exhibit quirks when switching from subsite to standalone mode, so all plugins have been disabled. Re-activate each plugin one-by-one and test
            the site after each activation. If you experience issues please see the
            <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-mu" target="_blank">Multisite Network FAQs</a> online.
        </div>

        <!-- WARN: SAFE MODE MESSAGES -->
        <div class="s4-warn margin-top-2" style="display:<?php echo ($safe_mode > 0 ? 'block' : 'none') ?>">
            <b>Safe Mode</b><br />
            Safe mode has <u>deactivated</u> all plugins. Please be sure to enable your plugins after logging in. <i>If you notice that problems arise when activating
                the plugins then active them one-by-one to isolate the plugin that could be causing the issue.</i>
        </div>
    </div>
</div>