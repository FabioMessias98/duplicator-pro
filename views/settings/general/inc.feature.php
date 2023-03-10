<?php
defined("ABSPATH") or die("");

DUP_PRO_U::hasCapability('manage_options');

$global = DUP_PRO_Global_Entity::get_instance();

$nonce_action    = 'duppro-settings-general-edit';
$action_updated  = null;
$action_response = DUP_PRO_U::__("Profile Settings Updated");
$dup_version     = DUPLICATOR_PRO_VERSION;

//SAVE RESULTS
if (isset($_REQUEST['action'])) {
    DUP_PRO_U::verifyNonce($_POST['_wpnonce'], $nonce_action);
    if ($_REQUEST['action'] == 'save') {
        $global->profile_idea = isset($_POST['_profile_idea']) ? 1 : 0;
        $global->profile_beta = isset($_POST['_profile_beta']) ? 1 : 0;
    }

    $action_updated = $global->save();
    $global->adjust_settings_for_system();
}

$import_tab_url   = 'admin.php?page=duplicator-pro-tools&tab=import';
$recovery_tab_url = 'admin.php?page=duplicator-pro-tools&tab=recovery';
?>

<style>
    td.profiles {padding-left: 20px}
    td.profiles p.description {margin:5px 0 20px 25px; max-width: 800px !important}
    sup.new-badge {background-color:maroon; border: maroon 1px solid; border-radius:8px; color:#fff; padding:1px 3px 2px 3px; margin:0; font-size:11px; line-height:11px; display:inline-block; font-style: normal}
    label.profile-type {font-size:16px !important; font-weight: bold}
    p.item {padding:0 0 15px 30px}
</style>

<form id="dup-settings-form" action="<?php echo self_admin_url('admin.php?page='.DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG); ?>" method="post" data-parsley-validate>
    <?php wp_nonce_field($nonce_action); ?>
    <input type="hidden" id="dup-settings-action" name="action" value="save">
    <input type="hidden" name="page" value="<?php echo DUP_PRO_Constants::$SETTINGS_SUBMENU_SLUG ?>">
    <input type="hidden" name="tab" value="general">
    <input type="hidden" name="subtab" value="profile">


    <?php if ($action_updated) : ?>
        <div class="notice notice-success is-dismissible dpro-wpnotice-box"><p><?php echo $action_response; ?></p></div>
    <?php endif; ?>

    <!-- ===============================
NEW FEATURES -->
    <table class="dup-pro-new-feathures" >
        <thead>
            <tr>
                <td colspan="2 ">
                    <label class="profile-type">
                        <?php DUP_PRO_U::esc_html_e("Release 4.0.0 Highlights"); ?>
                    </label><br>
                    <?php echo DUP_PRO_U::esc_html__("We've included some big features this release.  Here are the highlights:"); ?>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="icon" >
                    <i class="fas fa-arrow-alt-circle-down"></i>
                </td>
                <td>
                    <div>
                        <b><?php DUP_PRO_U::esc_html_e('Drag and Drop Install'); ?></b>
                    </div>
                    <?php
                    echo DUP_PRO_U::esc_html__('Drag a package to a destination WordPress to replace that site! See').
                    " <a href='{$import_tab_url}'>".DUP_PRO_U::esc_html__('Duplicator Pro > Tools > Import.').'</a>';
                    ?>
                </td>
            </tr>
            <tr>
                <td class="icon" >
                    <i class="fas fa-undo-alt"></i>
                </td>
                <td>
                    <div>
                        <b><?php DUP_PRO_U::esc_html_e('Recovery Point Support'); ?></b>
                    </div>
                    <?php
                    echo DUP_PRO_U::esc_html__('Quickly restore your site after a bad plugin update or manual change. See').
                    " <a href='${recovery_tab_url}'>".DUP_PRO_U::esc_html__('Duplicator Pro > Tools > Recovery.').'</a>';
                    ?>
                </td>
            </tr>
            <tr>
                <td class="icon" >
                    <i class="fa fa-info-circle"></i>
                </td>
                <td>
                    <div>
                        <b><?php DUP_PRO_U::esc_html_e('Installer Basic and Advanced Modes'); ?></b>
                    </div>
                    <?php
                    DUP_PRO_U::esc_html_e('The installer now includes two modes: "Basic" and "Advanced". '
                        .'Basic mode consists of only two pages for a streamlined experience, '
                        .'while Advanced mode is the classic four page install giving you extra configurability when you need it.');
                    ?>
                </td>
            </tr>
            <tr>
                <td class="icon" >
                    <i class="fa fa-hospital-symbol"></i>
                </td>
                <td>
                    <div>
                        <b><?php DUP_PRO_U::esc_html_e('Managed Hosting Support'); ?></b>
                    </div>
                    <ul>
                        <li><?php DUP_PRO_U::esc_html_e('Liquid web'); ?></li>
                        <li><?php DUP_PRO_U::esc_html_e('GoDaddy'); ?></li>
                        <li><?php DUP_PRO_U::esc_html_e('WpEngine'); ?></li>
                        <li><?php DUP_PRO_U::esc_html_e('Wordpress.com'); ?></li>
                        <li><?php DUP_PRO_U::esc_html_e('Pantheon'); ?></li>
                    </ul>
                </td>
            </tr>
        </tbody> 
    </table>
    <p>
        See <a href="https://snapcreek.com/duplicator/docs/changelog/" target="_blank">changelog</a> for complete list of new features and fixes in this release.  
    </p>

    <!-- ===============================
    RECENT FEATURES -->
    <!-- uncomment when have some <br/><hr class="separator"/>
    <label class="profile-type"><?php DUP_PRO_U::esc_html_e("Recent Features "); ?></label>
    <p class="item">
    <?php
    $storageLink = admin_url("admin.php?page=duplicator-pro-storage&tab=storage&inner_page=edit");
    $storageLink = wp_nonce_url($storageLink, 'storage-edit');
    echo wp_kses(DUP_PRO_U::__("<sup class='new-badge'>new</sup> <b>OneDrive for Business Support</b> - Use Microsoft OneDrive for Business to store and manage packages. Note:One Drive Personal support was already present."), array(
        'sup' => array(),
        'b'   => array(),
    ));
    echo '<br/> ';
    echo wp_kses(sprintf(DUP_PRO_U::__("<small>Go to <a href='%s'>Storage > Add New</a> to setup.</small>"), $storageLink), array(
        'small' => array(),
        'a'     => array('href'),
    ));
    ?>
    </p>-->
    <hr class="separator"/>
    <!-- ===============================
    FEATURE SURVEY -->
    <label class="profile-type"><?php DUP_PRO_U::esc_html_e("Want a New Feature?") ?></label><br/>
    <?php
    echo '<strong><a target="blank" href="https://snapcreek.com/prosurvey">'.
    DUP_PRO_U::__('Just answer this single question').'</a></strong>'.DUP_PRO_U::__(' to tell us what feature you want added!');

    // Remove this to enable the beta feathures section
    echo '</form>';
    return;
    ?>

    <!-- ===============================
    EXPERIMENTAL FEATURES -->
    <br/><hr class="separator"/>
    <!--<label class="profile-type"><?php DUP_PRO_U::esc_html_e("Experimental Features"); ?></label><br/>-->
    <label class="profile-type"><?php DUP_PRO_U::esc_html_e("Beta Features"); ?></label><br/>

    <?php
    //DUP_PRO_U::esc_html_e("Beta and Design Concepts sections let you preview upcoming features the Duplicator team is working on. Check the feature sections you would like to enable.");
    DUP_PRO_U::esc_html_e("Beta features are considered experimental and should not be enabled on production sites.");
    ?>
    <br/><br/>


    <div style="padding:0 0 0 30px">
        <!-- ================
        BETA -->
        <input type="checkbox" name="_profile_beta" id="_profile_beta" <?php echo DUP_PRO_UI::echoChecked($global->profile_beta); ?> />
        <label for="_profile_beta" class="profile-type"><?php DUP_PRO_U::esc_html_e("Enable"); ?></label>
        <i class="fas fa-question-circle fa-sm"
           data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Debug views"); ?>"
           data-tooltip="<?php
           DUP_PRO_U::esc_attr_e('Checking this checkbox will enable various beta features.  These features should NOT be used in production environments.  Please '
               .'let us know your thoughts and report any issue encountered.  This will help to more quickly get the feature out of Beta.');
           ?>"></i>

        <p class="item">
            <?php
            $importURL = self_admin_url()."admin.php?page=".DUP_PRO_Constants::$TOOLS_SUBMENU_SLUG.'&tab=import';

            echo wp_kses(DUP_PRO_U::__("<b>Drag & Drop Install:</b> Overwrite a site by dragging a package into the plugin. No need to FTP a package!"), array(
                'b' => array(),
                )
            );
            echo '<br/>';
            echo wp_kses(sprintf(DUP_PRO_U::__("<small>Go to <a href='%s'>Tools > Import</a> to overwrite the current site. </small>"), $importURL), array(
                'a'     => array(
                    'href'  => array(),
                    'title' => array()
                ),
                'small' => array(),
                )
            );
            ?>
        </p>


        <br/>
        <!-- ================
        DESIGN CONCEPTS 
        <input type="checkbox" name="_profile_idea" id="_profile_idea" <?php echo DUP_PRO_UI::echoChecked($global->profile_idea); ?> />
        <label for="_profile_idea" class="profile-type"><?php DUP_PRO_U::esc_html_e("Design Concepts"); ?></label>
        <i class="fas fa-question-circle fa-sm"
            data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Concept Views"); ?>"
            data-tooltip="<?php
        DUP_PRO_U::esc_attr_e('Checking this checkbox will enable various idea design concepts.  These features MAY NOT function fully and should '
            .'NEVER be used in production enviroments.  In some cases the features will simply just be UI mockups.  Please let us know what you think of the concepts as '
            .'they may eventually become features. ');
        ?>"></i>
    
        <p class="item">
        <?php
        DUP_PRO_U::esc_html_e('No design concept features in this release.');
        ?>
        </p>
        -->

    </div>
    <p class="submit" style="margin:5px 0px 0xp 5px;">
        <input type="submit" name="submit" id="submit" class="button-primary" value="<?php DUP_PRO_U::esc_attr_e('Save Feature Settings') ?>" style="display: inline-block;" />
    </p>

</form>
