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

$dupDatabase          = 'dup-database__'.DUPX_ArchiveConfig::getInstance()->package_hash.'.sql';
$dupDatabaseDupFolder = basename(DUPX_INIT).'/'.$dupDatabase;

if (!$extraData['validCharset'] && $extraData['validCollate']) {
    $invalidCheckboxTitle = '"Legacy Character set"';
    $subTitle             = 'character set';
} else if ($extraData['validCharset'] && !$extraData['validCollate']) {
    $invalidCheckboxTitle = '"Legacy Collation"';
    $subTitle             = 'collation';
} else if (!$extraData['validCharset'] && !$extraData['validCollate']) {
    $invalidCheckboxTitle = '"Legacy Character set" and "Legacy Collation"';
    $subTitle             = 'character set and collation';
} else {
    $invalidCheckboxTitle = '';
    $subTitle             = '';
}
?>
<div class="sub-title">STATUS</div>
<p class="<?php echo $statusClass; ?>">
    <?php if ($isOk) { ?>
        Character set and Collate test passed! This database supports the required table character sets and collations.
        <?php
    } if (DUPX_InstallerState::isImportFromBackendMode()) {
        echo htmlentities($subTitle);
        ?> 
        isn't supported on current database. It is not possible to import this package from the wordpress backend but you can do so by running a normal installation. 
    <?php } if (DUPX_InstallerState::isAdvacnedMode()) { ?>
        Please check the <?php echo htmlentities($invalidCheckboxTitle); ?> checkbox above under options and then click the 'Test Database' link.<br/>
        <small>
            Details: The database where the package was created has a <?php echo htmlentities($subTitle); ?> that is not supported on this server. 
            This issue happens when a site is moved from an older version of MySQL to a newer version of MySQL. 
            The recommended fix is to update MySQL on this server to support the character set that is failing below. 
            If that is not an option for your host then continue by clicking the <?php echo htmlentities($invalidCheckboxTitle); ?> checkbox above. 
            For more details about this issue and other details regarding this issue see the FAQ link below.
        </small>
        <?php
    } else {
        echo htmlentities($subTitle);
        ?>
        isn't supported on current database. Switch to advanced mode an revalidate it to continue the installation
    <?php } ?>
</p>
<?php if (!empty($errorMessage)) { ?>
    <p>
        Error detail: <span class="red" ><?php echo htmlentities($errorMessage); ?></span>
    </p>
<?php } ?>

<div class="sub-title">DETAILS</div>
<p>
    This test checks to make sure this database can support the character set and collations found in the <?php echo htmlentities($dupDatabaseDupFolder); ?> script.
</p>

<b>Character set in <?php echo htmlentities($dupDatabase); ?></b> <br/>
<table class="charset-list">
    <?php if (empty($extraData['charsetStatus'])) { ?>
        <tr><td style='font-weight:normal'>This test was not ran.</td></tr>
        <?php
    } else {
        foreach ($extraData['charsetStatus'] as $item) {
            ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><span class="status-badge <?php echo $item['found'] ? 'pass' : 'fail'; ?>"></td>
            </tr>
            <?php
        }
    }
    ?>
</table>
<p></p>
<b>Collations in <?php echo htmlentities($dupDatabase); ?></b> <br/>
<table class="collation-list">
    <?php if (empty($extraData['collateStatus'])) { ?>
        <tr><td style='font-weight:normal'>This test was not ran.</td></tr>
        <?php
    } else {
        foreach ($extraData['collateStatus'] as $item) {
            ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><span class="status-badge <?php echo $item['found'] ? 'pass' : 'fail'; ?>"></td>
            </tr>
            <?php
        }
    }
    ?>
</table>
<p></p>
<div class="sub-title">TROUBLESHOOT</div>
<ul>
    <li><i class="far fa-file-code"></i> <a href='<?php echo DUPX_U::esc_attr(DUPX_Constants::FAQ_URL); ?>#faq-installer-110-q' target='_help'>What is Compatibility mode & 'Unknown Collation' errors?</a></li>
</ul>

