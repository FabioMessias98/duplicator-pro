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
final class DUPX_Paramas_Descriptor_cpanel implements DUPX_Interface_Paramas_Descriptor
{

    const INVALID_EMPTY = 'can\'t be empty';

    /**
     *
     * @param DUPX_Param_item[] $params
     */
    public static function init(&$params)
    {
        $params[DUPX_Paramas_Manager::PARAM_CPNL_CAN_SELECTED] = new DUPX_Param_item(
            DUPX_Paramas_Manager::PARAM_CPNL_CAN_SELECTED,
            DUPX_Param_item::TYPE_BOOL,
            array(
            'default' => true
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_HOST] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_HOST,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => "https://{$GLOBALS['HOST_NAME']}:2083",
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            'validateCallback' => array(__CLASS__, 'validateNoEmptyIfCpanel'),
            'invalidMessage'   => self::INVALID_EMPTY
            ),
            array(
            'label'            => 'CPanel host:',
            'wrapperClasses'   => array('revalidate-on-change'),
            'attr'             => array(
                'required'    => 'true',
                'placeholder' => 'cPanel url'
            ),
            'subNote'          => '<span id="cpnl-host-warn">'
            .'Caution: The cPanel host name and URL in the browser address bar do not match, '
            .'in rare cases this may be intentional.'
            .'Please be sure this is the correct server to avoid data loss.</span>',
            'postfixElement'   => 'button',
            'postfixElemLabel' => 'get',
            'postfixBtnAction' => 'DUPX.getcPanelURL(this);'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_USER] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_USER,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_TEXT,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            'validateCallback' => array(__CLASS__, 'validateNoEmptyIfCpanel'),
            'invalidMessage'   => self::INVALID_EMPTY
            ),
            array(
            'label'          => 'CPanel username:',
            'wrapperClasses' => array('revalidate-on-change'),
            'attr'           => array(
                'required'             => 'required',
                'data-parsley-pattern' => '/^[\w.-~]+$/',
                'placeholder'          => 'cPanel username'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_PASS] = new DUPX_Param_item_form_pass(
            DUPX_Paramas_Manager::PARAM_CPNL_PASS,
            DUPX_Param_item_form_pass::TYPE_STRING,
            DUPX_Param_item_form_pass::FORM_TYPE_PWD_TOGGLE,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
            'validateCallback' => array(__CLASS__, 'validateNoEmptyIfCpanel'),
            'invalidMessage'   => self::INVALID_EMPTY
            ),
            array(
            'label'          => 'CPanel password:',
            'wrapperClasses' => array('revalidate-on-change'),
            'attr'           => array(
                'required'    => 'true',
                'placeholder' => 'cPanel password'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_ACTION] = $params[DUPX_Paramas_Manager::PARAM_DB_ACTION]->getCopyWithNewName(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_ACTION,
            array(),
            array(
                'status' => DUPX_Param_item_form::STATUS_DISABLED
        ));
        // force create database enable for cpanel
        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_ACTION]->setOptionStatus(0, DUPX_Param_item_form_option::OPT_ENABLED);

        $params[DUPX_Paramas_Manager::PARAM_CPNL_PREFIX] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_PREFIX,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_HIDDEN,
            array(
            'default'          => '',
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_HOST] = $params[DUPX_Paramas_Manager::PARAM_DB_HOST]->getCopyWithNewName(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_HOST,
            array(
                'default'          => 'localhost',
                'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
                'validateCallback' => array(__CLASS__, 'validateNoEmptyIfCpanel'),
                'invalidMessage'   => self::INVALID_EMPTY
            ),
            array(
                'status' => DUPX_Param_item_form::STATUS_DISABLED,
                'attr'   => array(
                    'required' => 'true'
                )
        ));

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_SEL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_SEL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label'          => 'Database:',
            'wrapperClasses' => array('revalidate-on-change'),
            'status'         => DUPX_Param_item_form::STATUS_DISABLED,
            'attr'           => array(
                'required'             => 'true',
                'data-parsley-pattern' => '^((?!-- Select Database --).)*$'
            ),
            'subNote'        => '<span class="s2-warning-emptydb">'
            .'Warning: The selected "Action" above will remove <u>all data</u> from this database!'
            .'</span>'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_TXT] = $params[DUPX_Paramas_Manager::PARAM_DB_NAME]->getCopyWithNewName(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_NAME_TXT,
            array(
                'default'          => '',
                'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
                'validateCallback' => function ($value) {
                    $paramManager = DUPX_Paramas_Manager::getInstance();
                    if ($paramManager->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE) === 'cpnl' &&
                        $paramManager->getValue(DUPX_Paramas_Manager::PARAM_CPNL_DB_ACTION) === DUPX_DBInstall::DBACTION_CREATE) {
                        return DUPX_Paramas_Descriptors::validateNotEmpty($value);
                    } else {
                        $value = '';
                        return true;
                    }
                },
                'invalidMessage' => self::INVALID_EMPTY
            ),
            array(
                'label'           => 'Database:',
                'status'          => DUPX_Param_item_form::STATUS_DISABLED,
                'attr'            => array(
                    'required'                      => 'true',
                    'data-parsley-pattern'          => '/^[\w.-~]+$/',
                    'data-parsley-errors-container' => '#cpnl-dbname-txt-error'
                ),
                'subNote'         => '<span id="cpnl-dbname-txt-error"></span>',
                'prefixElement'   => 'label',
                'prefixElemLabel' => '',
                'prefixElemId'    => 'cpnl-prefix-dbname'
        ));

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_SEL] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_SEL,
            DUPX_Param_item_form::TYPE_STRING,
            DUPX_Param_item_form::FORM_TYPE_SELECT,
            array(
            'default'          => null,
            'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline')
            ),
            array(
            'label'          => 'User:',
            'wrapperClasses' => array('revalidate-on-change'),
            'status'         => DUPX_Param_item_form::STATUS_DISABLED,
            'attr'           => array(
                'required'             => 'true',
                'data-parsley-pattern' => '^((?!-- Select User --).)*$'
            )
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_TXT] = $params[DUPX_Paramas_Manager::PARAM_DB_USER]->getCopyWithNewName(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_TXT,
            array(
                'default'          => '',
                'sanitizeCallback' => array('DupProSnapLibUtil', 'sanitize_non_stamp_chars_and_newline'),
                'validateCallback' => function ($value) {
                    $paramManager = DUPX_Paramas_Manager::getInstance();
                    if ($paramManager->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE) === 'cpnl' &&
                        $paramManager->getValue(DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_CHK) === true) {
                        return DUPX_Paramas_Descriptors::validateNotEmpty($value);
                    } else {
                        $value = '';
                        return true;
                    }
                },
                'invalidMessage' => self::INVALID_EMPTY
            ),
            array(
                'label'           => 'User:',
                'status'          => DUPX_Param_item_form::STATUS_DISABLED,
                'attr'            => array(
                    'required'                      => 'true',
                    'data-parsley-pattern'          => '/^[a-zA-Z0-9-_]+$/',
                    'data-parsley-errors-container' => '#cpnl-dbuser-txt-error',
                    'data-parsley-cpnluser'         => "16"
                ),
                'subNote'         => '<span id="cpnl-dbuser-txt-error"></span>',
                'prefixElement'   => 'label',
                'prefixElemLabel' => '',
                'prefixElemId'    => 'cpnl-prefix-dbuser',
        ));

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_CHK] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_USER_CHK,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'          => ' ',
            'wrapperClasses' => array('revalidate-on-change'),
            'checkboxLabel'  => 'Create New Database User'
            )
        );

        $params[DUPX_Paramas_Manager::PARAM_CPNL_DB_PASS] = $params[DUPX_Paramas_Manager::PARAM_DB_PASS]->getCopyWithNewName(
            DUPX_Paramas_Manager::PARAM_CPNL_DB_PASS,
            array(),
            array(
                'status' => DUPX_Param_item_form::STATUS_DISABLED,
                'attr'   => array(
                    'required' => 'true'
                )
        ));

        $params[DUPX_Paramas_Manager::PARAM_CPNL_IGNORE_PREFIX] = new DUPX_Param_item_form(
            DUPX_Paramas_Manager::PARAM_CPNL_IGNORE_PREFIX,
            DUPX_Param_item_form::TYPE_BOOL,
            DUPX_Param_item_form::FORM_TYPE_CHECKBOX,
            array(
            'default' => false
            ),
            array(
            'label'          => 'CPanel Prefix',
            'wrapperClasses' => array('revalidate-on-change'),
            'checkboxLabel'  => 'Ignore',
            'attr'           => array(
                'onclick' => 'DUPX.cpnlPrefixIgnore();'
            )
            )
        );
    }

    public static function validateNoEmptyIfCpanel($value)
    {
        if (DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_DB_VIEW_MODE) === 'cpnl') {
            return DUPX_Paramas_Descriptors::validateNotEmpty($value);
        } else {
            $value = '';
            return true;
        }
    }

    /**
     * 
     * @param DUPX_Param_item[] $params
     */
    public static function updateParamsAfterOverwrite(&$params)
    {
        
    }
}