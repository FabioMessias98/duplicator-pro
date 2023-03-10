<?php
/**
 * Installer params manager
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @final class DUPX_Paramas_Descriptor_urls_paths
  {
  package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class DUPX_Paramas_Descriptor_database implements DUPX_Interface_Paramas_Descriptor
{

    const INVALID_EMPTY = 'can\'t be empty';

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $params[DUPX_Paramas_Manager::PARAM_DB_DISPLAY_OVERWIRE_WARNING] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_DB_DISPLAY_OVERWIRE_WARNING,
            DUPX_Param_item::TYPE_BOOL,
            array(
            'default' => true
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_BGROUP,
            array(
            'default'      => 'basic',
            'acceptValues' => array(
                'basic',
                'cpnl'
            )
            ),
            array(
            'label'                 => '',
            'options'               => array(
                new DUPX_Param_item_form_option('basic', 'Default'),
                new DUPX_Param_item_form_option('cpnl', 'CPanel')
            ),
            'wrapperClasses'        => array('revalidate-on-change', 'align-right'),
            'inputContainerClasses' => array('small')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_HOST] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_HOST,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'persistence'      => true,
            'default'          => 'localhost',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            'validateCallback' => array(__CLASS__, 'validateNoEmptyIfBasic'),
            'invalidMessage'   => self::INVALID_EMPTY
            ),
            array(
            'label'          => 'Host:',
            'wrapperClasses' => array('revalidate-on-change'),
            'attr'           => array(
                'required'    => 'required',
                'placeholder' => 'localhost'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_NAME] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_NAME,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'persistence'      => true,
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            'validateCallback' => array(__CLASS__, 'validateNoEmptyIfBasic'),
            'invalidMessage'   => self::INVALID_EMPTY
            ),
            array(
            'label'          => 'Database:',
            'wrapperClasses' => array('revalidate-on-change'),
            'attr'           => array(
                'required'    => 'required',
                'placeholder' => 'new or existing database name'
            ),
            'subNote'        => <<<NOTE
<span class="s2-warning-emptydb">
    Warning: The selected 'Action' above will remove <u>all data</u> from this database!
</span>
<span class="s2-warning-renamedb">
    Notice: The selected 'Action' will rename <u>all existing tables</u> from the database name above with a prefix {$GLOBALS['DB_RENAME_PREFIX']}
    The prefix is only applied to existing tables and not the new tables that will be installed.
</span>
<span class="s2-warning-manualdb">
    Notice: The 'Manual SQL execution' action will prevent the SQL script in the archive from running. The database above should already be
    pre-populated with data which will be updated in the next step. No data in the database will be modified until after Step 3 runs.
</span>
NOTE
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_USER] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_USER,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'persistence'      => true,
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            'validateCallback' => array(__CLASS__, 'validateNoEmptyIfBasic'),
            'invalidMessage'   => self::INVALID_EMPTY
            ),
            array(
            'label'          => 'User:',
            'wrapperClasses' => array('revalidate-on-change'),
            'attr'           => array(
                'placeholder'  => 'valid database username',
                // Can be written field wise
                // Ref. https://developer.mozilla.org/en-US/docs/Web/Security/Securing_your_site/Turning_off_form_autocompletion
                'autocomplete' => "off"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_PASS] = new DUPX_Param_item_form_pass(
            DUPX_Paramas_Manager::PARAM_DB_PASS,
            DUPX_Param_item_form_pass::TYPE_STRING,
            DUPX_Param_item_form_pass::FORM_TYPE_PWD_TOGGLE,
            array(
            'persistence'      => true,
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label'          => 'Password:',
            'wrapperClasses' => array('revalidate-on-change'),
            'attr'           => array(
                'placeholder'  => 'valid database user password',
                // Can be written field wise
                // Ref. https://devBasicBasiceloper.mozilla.org/en-US/docs/Web/Security/Securing_your_site/Turning_off_form_autocompletion
                'autocomplete' => "off"
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_CHARSET] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_CHARSET,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => $archiveConfig->getWpConfigDefineValue('DB_CHARSET', $GLOBALS['DBCHARSET_DEFAULT']),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => DUPX_Param_item_form::VALIDATE_REGEX_AZ_NUMBER_SEP
            ),
            array(
            'label'  => 'Charset:',
            'status' => function () {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'wrapperClasses' => array('revalidate-on-change')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_COLLATE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_COLLATE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => $archiveConfig->getWpConfigDefineValue('DB_COLLATE', ''),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => DUPX_Param_item_form::VALIDATE_REGEX_AZ_NUMBER_SEP
            ),
            array(
            'label'  => 'Collation:',
            'status' => function () {
                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_ENABLED;
                }
            },
            'wrapperClasses' => array('revalidate-on-change')
            )
        );

        $tablePrefixWarning = "Changing this setting alters the database table prefix by renaming all tables and references to them.\n"
            ."Change it only if you're sure you know what you're doing!";

        $params[DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => DUPX_ArchiveConfig::getInstance()->wp_tableprefix,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            'validateRegex'    => DUPX_Param_item_form::VALIDATE_REGEX_AZ_NUMBER_SEP
            ),
            array(
            'status' => function () {
                $archiveConfig = DUPX_ArchiveConfig::getInstance();
                if ($archiveConfig->getLicenseType() < DUPX_LicenseType::Freelancer) {
                    DUPX_Param_item_form::STATUS_INFO_ONLY;
                }

                if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_RESTORE_BACKUP_MODE)) {
                    return DUPX_Param_item_form::STATUS_DISABLED;
                } else {
                    return DUPX_Param_item_form::STATUS_READONLY;
                }
            },
            'label'            => 'Table Prefix:',
            'wrapperClasses'   => array('revalidate-on-change'),
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'edit',
            'postfixBtnAction' => 'DUPX.editActivate(this, '.DupProSnapJsonU::wp_json_encode($tablePrefixWarning).');',
            'subNote'          => $archiveConfig->getLicenseType() >= DUPX_LicenseType::Freelancer ? '' : 'Changing the prefix is only available with Freelancer, Business or Gold licenses'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Legacy Character set:',
            'checkboxLabel' => 'Enable legacy character set support for unknown character sets.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_CHARSET_FB_VAL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default' => '',
            ),
            array(
            'label'   => ' ',
            'options' => array(),
            'subNote' => 'This option is populated after clicking on the "Test Database" button.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Legacy Collation:',
            'checkboxLabel' => 'Enable legacy collation fallback support for unknown collations types.',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_COLLATE_FB_VAL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default' => '',
            ),
            array(
            'label'   => ' ',
            'options' => array(),
            'subNote' => 'This option is populated after clicking on the "Test Database" button.',
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_SPACING] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_SPACING,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'         => 'Spacing:',
            'checkboxLabel' => 'Enable non-breaking space characters fix.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_VIEW_CREATION] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_VIEW_CREATION,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => true
            ),
            array(
            'label'         => 'Views:',
            'checkboxLabel' => 'Enable View Creation.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_PROC_CREATION] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_PROC_CREATION,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => true
            ),
            array(
            'label'         => 'Stored Procedures:',
            'checkboxLabel' => 'Enable Stored Procedure Creation.'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'validateRegex'    => '/^[A-Za-z0-9_\-,]*$/', // db options with , and can be empty
            'sanitizeCallback' => function ($value) {
                $value = DupProSnapLibUtil::sanitize_non_stamp_chars_newline_and_trim($value);
                return str_replace(' ', '', $value);
            },
            ),
            array(
            'label'          => ' ', // for aligment at PARAM_DB_MYSQL_MODE
            'wrapperClasses' => 'no-display',
            'subNote'        => 'Separate additional '.DUPX_View_Funcs::helpLink('step2', 'sql modes', false).' with commas &amp; no spaces.<br>'
            .'Example: <i>NO_ENGINE_SUBSTITUTION,NO_ZERO_IN_DATE,...</i>.</small>'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_RADIO,
            array(
            'default'      => 'DEFAULT',
            'acceptValues' => array(
                'DEFAULT',
                'DISABLE',
                'CUSTOM'
            )
            ),
            array(
            'label'   => 'Mysql Mode:',
            'options' => array(
                new DUPX_Param_item_form_option('DEFAULT', 'Default', DUPX_Param_item_form_option::OPT_ENABLED, array(
                    'onchange' => "if ($(this).is(':checked')) { "
                    ."jQuery('#".$params[DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS]->getFormWrapperId()."').addClass('no-display');"
                    ."}"
                    )),
                new DUPX_Param_item_form_option('DISABLE', 'Disable', DUPX_Param_item_form_option::OPT_ENABLED, array(
                    'onchange' => "if ($(this).is(':checked')) { "
                    ."jQuery('#".$params[DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS]->getFormWrapperId()."').addClass('no-display');"
                    ."}"
                    )),
                new DUPX_Param_item_form_option('CUSTOM', 'Custom', DUPX_Param_item_form_option::OPT_ENABLED, array(
                    'onchange' => "if ($(this).is(':checked')) { "
                    ."jQuery('#".$params[DUPX_Paramas_Manager::PARAM_DB_MYSQL_MODE_OPTS]->getFormWrapperId()."').removeClass('no-display');"
                    ."}")),
            ))
        );

        $params[DUPX_Paramas_Manager::PARAM_DB_TABLES] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_DB_TABLES,
            DUPX_Param_item_form::TYPE_ARRAY_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(// ITEM ATTRIBUTES
            'default'          => self::getTableList(),
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_newline_and_trim'),
            ), array(// FORM ATTRIBUTES
            'multiple' => true,
            'size'     => 10,
            'options'  => function ($paramObj) {
                $result        = array();
                $paramsManager = DUPX_Paramas_Manager::getInstance();
                $subsiteId     = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_SUBSITE_ID);
                $newPrefix     = $paramsManager->getValue(DUPX_Paramas_Manager::PARAM_DB_TABLE_PREFIX);

                foreach (DUPX_Paramas_Descriptor_database::getLabelTablesList($newPrefix, $subsiteId) as $label => $tableName) {
                    $result[] = new DUPX_Param_item_form_option($tableName, $label);
                }
                return $result;
            }
            )
        );
    }

    public static function validateNoEmptyIfBasic($value)
    {
        if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE) === 'basic') {
            return DUPX_Paramas_Descriptors::validateNotEmpty($value);
        } else {
            $value = '';
            return true;
        }
    }

    public static function getLabelTablesList($newPrefix = null, $subsiteId = false)
    {
        $sharedTables  = array();
        $subSiteTables = array();
        $finalTables   = array();

        $archive_config = DUPX_ArchiveConfig::getInstance();
        $oldPrefix      = $archive_config->wp_tableprefix;
        $tablePrefix    = is_null($newPrefix) ? $oldPrefix : $newPrefix;
        $tables         = (array) $archive_config->dbInfo->tablesList;
        $installType    = ($subsiteId !== false && $subsiteId > 0) ? 1 : 0;

        // there is only one `users` and `usermeta` table in multisite installation
        $generalTables = array(
            $tablePrefix.'commentmeta',
            $tablePrefix.'comments',
            $tablePrefix.'links',
            $tablePrefix.'options',
            $tablePrefix.'postmeta',
            $tablePrefix.'posts',
            $tablePrefix.'term_relationships',
            $tablePrefix.'term_taxonomy',
            $tablePrefix.'terms',
            $tablePrefix.'termmeta'
        );

        $multisiteOnlyTables = array(
            $tablePrefix.'blogmeta',
            $tablePrefix.'blogs',
            $tablePrefix.'blog_versions',
            $tablePrefix.'registration_log',
            $tablePrefix.'signups',
            $tablePrefix.'site',
            $tablePrefix.'sitemeta'
        );

        /**
         * $pattern_shared_tables: match tables starting with $tablePrefix and not followed
         * by a number following a `_` character e.g. `wp_users`, `wp_duplicator_pro_entities`
         * $pattern_subsite_tables: match tables starting with $subsiteTablePrefix
         * and what the one above does e.g. `wp_3_posts`, `wp_3_comments`, `wp_users` tables
         */
        $subsiteTablePrefix     = "{$tablePrefix}{$subsiteId}_";
        $qTablePrefix           = preg_quote($tablePrefix, "/");
        $qSubsiteTablePrefix    = preg_quote($subsiteTablePrefix, "/");
        $pattern_shared_tables  = "^{$qTablePrefix}(?!\d+_)";
        $pattern_subsite_tables = "^{$qSubsiteTablePrefix}(.+)";

        foreach ($tables as $table => $tableInfo) {
            if ($oldPrefix == $tablePrefix || (strpos($table, $oldPrefix) !== 0)) {
                $tableNew = $table;
            } else {
                $tableNew = $tablePrefix.substr($table, strlen($oldPrefix));
            }

            //rename subsite tables before adding options if it's a standalone installation
            if ($installType === 1) {
                //skip multi-site only and general tables
                if (in_array($tableNew, $multisiteOnlyTables) || in_array($tableNew, $generalTables)) {
                    continue;
                }

                //get tables shared between all sub-sites
                if (preg_match("/{$pattern_shared_tables}/", $tableNew)) {
                    $sharedTables[$tableNew.' ('.$tableInfo->rows.')'] = $tableNew;
                }

                if ($subsiteId !== 1) {
                    //get tables unique to the given sub-site
                    if (preg_match("/{$pattern_subsite_tables}/", $tableNew)) {
                        $tableNew                                           = preg_replace("/^{$qSubsiteTablePrefix}/", $tablePrefix, $tableNew);
                        $subSiteTables[$tableNew.' ('.$tableInfo->rows.')'] = $tableNew;
                    }
                }
            } else {
                //if not sub-site to standalone installation keep all tables
                $finalTables[$tableNew.' ('.$tableInfo->rows.')'] = $tableNew;
            }
        }

        if ($installType === 1) {
            if ($subsiteId === 1) {
                //if root site, just add general tables to shared tables
                $finalTables = array_merge($sharedTables, $generalTables);
            } else {
                //remove tables that are duplicates e.g. wp_2_table and wp_table
                $uniqueSharedTables = array_diff($sharedTables, $subSiteTables);
                $finalTables        = array_merge($subSiteTables, $uniqueSharedTables);
            }
        }
        return $finalTables;
    }

    public static function getTableList($newPrefix = null, $subsiteId = false)
    {
        $result = array();
        foreach (self::getLabelTablesList($newPrefix, $subsiteId) as $tableName) {
            $result[] = $tableName;
        }
        return $result;
    }

    /**
     * 
     * @param DUPX_Param_item[] $params
     */
    public static function updateParamsAfterOverwrite(&$params)
    {
        
    }
}