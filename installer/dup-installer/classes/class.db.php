<?php
/**
 * Lightweight abstraction layer for common simple database routines
 *
 * Standard: PSR-2
 *
 * @package SC\DUPX\DB
 * @link http://www.php-fig.org/psr/psr-2/
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_DB
{

    /**
     * Modified version of https://developer.wordpress.org/reference/classes/wpdb/db_connect/
     *
     * @param string $host The server host name
     * @param string $username The server DB user name
     * @param string $password The server DB password
     * @param string $dbname The server DB name
     * @return mysqli Database connection handle
     */
    public static function connect($host, $username, $password, $dbname = null)
    {
        $dbh = null;
        try {
            $port    = null;
            $socket  = null;
            $is_ipv6 = false;

            $host_data = self::parseDBHost($host);
            if ($host_data) {
                list($host, $port, $socket, $is_ipv6) = $host_data;
            }

            /*
             * If using the `mysqlnd` library, the IPv6 address needs to be
             * enclosed in square brackets, whereas it doesn't while using the
             * `libmysqlclient` library.
             * @see https://bugs.php.net/bug.php?id=67563
             */
            if ($is_ipv6 && extension_loaded('mysqlnd')) {
                $host = "[$host]";
            }

            $dbh = @mysqli_connect($host, $username, $password, $dbname, $port, $socket);

            if (!$dbh) {
                DUPX_Log::info('DATABASE CONNECTION ERROR: '.mysqli_connect_error().'[ERRNO:'.mysqli_connect_errno().']');
            } else {
                if (method_exists($dbh, 'options')) {
                    $dbh->options(MYSQLI_OPT_LOCAL_INFILE, false);
                }
            }
        }
        catch (Exception $e) {
            DUPX_Log::info('DATABASE CONNECTION EXCEPTION ERROR: '.$e->getMessage());
        }
        return $dbh;
    }

    /**
     * Modified version of https://developer.wordpress.org/reference/classes/wpdb/parse_db_host/
     *
     * @param string $host The DB_HOST setting to parse
     * @return array|bool Array containing the host, the port, the socket and whether it is an IPv6 address, in that order. If $host couldn't be parsed, returns false
     */
    public static function parseDBHost($host)
    {
        $port    = null;
        $socket  = null;
        $is_ipv6 = false;

        // First peel off the socket parameter from the right, if it exists.
        $socket_pos = strpos($host, ':/');
        if (false !== $socket_pos) {
            $socket = substr($host, $socket_pos + 1);
            $host   = substr($host, 0, $socket_pos);
        }

        // We need to check for an IPv6 address first.
        // An IPv6 address will always contain at least two colons.
        if (substr_count($host, ':') > 1) {
            $pattern = '#^(?:\[)?(?P<host>[0-9a-fA-F:]+)(?:\]:(?P<port>[\d]+))?#';
            $is_ipv6 = true;
        } else {
            // We seem to be dealing with an IPv4 address.
            $pattern = '#^(?P<host>[^:/]*)(?::(?P<port>[\d]+))?#';
        }

        $matches = array();
        $result  = preg_match($pattern, $host, $matches);

        if (1 !== $result) {
            // Couldn't parse the address, bail.
            return false;
        }

        $host = '';
        foreach (array('host', 'port') as $component) {
            if (!empty($matches[$component])) {
                $$component = $matches[$component];
            }
        }

        return array($host, $port, $socket, $is_ipv6);
    }

    /**
     * 
     * @param string    $host       The server host name
     * @param string    $username   The server DB user name
     * @param string    $password   The server DB password
     * @param string    $dbname     The server DB name
     * 
     * @return boolean
     */
    public static function testConnection($host, $username, $password, $dbname = '')
    {
        if (($dbh = DUPX_DB::connect($host, $username, $password, $dbname))) {
            mysqli_close($dbh);
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Count the tables in a given database
     *
     * @param obj    $dbh       A valid database link handle
     * @param string $dbname    Database to count tables in
     *
     * @return int  The number of tables in the database
     */
    public static function countTables($dbh, $dbname)
    {
        $res = self::mysqli_query($dbh, "SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = '".mysqli_real_escape_string($dbh, $dbname)."' ");
        $row = mysqli_fetch_row($res);
        return is_null($row) ? 0 : $row[0];
    }

    /**
     * Returns the number of rows in a table
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $name	A valid table name
     */
    public static function countTableRows($dbh, $name)
    {
        $total = self::mysqli_query($dbh, "SELECT COUNT(*) FROM `".mysqli_real_escape_string($dbh, $name)."`");
        if ($total) {
            $total = @mysqli_fetch_array($total);
            return $total[0];
        } else {
            return 0;
        }
    }

    /**
     * Get default character set
     *
     * @param obj $dbh   A valid database link handle
     * @return string	 Default charset
     */
    public static function getDefaultCharSet($dbh)
    {
        static $defaultCharset = null;
        if (is_null($defaultCharset)) {
            $query = 'SHOW VARIABLES LIKE "character_set_database"';

            if (($result = self::mysqli_query($dbh, $query))) {
                if (($row = $result->fetch_assoc())) {
                    $defaultCharset = $row["Value"];
                }
                $result->free();
            } else {
                $defaultCharset = '';
            }
        }

        return $defaultCharset;
    }

    /**
     * Get Supported charset list
     *
     * @param obj $dbh   A valid database link handle
     * @return array	 Supported charset list
     */
    public static function getSupportedCharSetList($dbh)
    {
        static $charsetList = null;
        if (is_null($charsetList)) {
            $charsetList = array();
            $query       = "SHOW CHARACTER SET;";

            if (($result = self::mysqli_query($dbh, $query))) {
                while ($row = $result->fetch_assoc()) {
                    $charsetList[] = $row["Charset"];
                }
                $result->free();
            }
            sort($charsetList);
        }
        return $charsetList;
    }

    /**
     * Get Supported collations along with character set
     *
     * @param obj $dbh   A valid database link handle
     * @return array	 Supported collation
     */
    public static function getSupportedCollates($dbh)
    {
        static $collations = null;
        if (is_null($collations)) {
            $collations = array();
            $query      = "SHOW COLLATION";
            if (($result     = self::mysqli_query($dbh, $query))) {
                while ($row = $result->fetch_assoc()) {
                    $collations[] = $row;
                }
                $result->free();
            }

            usort($collations, function ($a, $b) {
                return strcmp($a['Collation'], $b['Collation']);
            });
        }
        return $collations;
    }

    /**
     * Get Supported collations along with character set
     *
     * @param obj $dbh   A valid database link handle
     * @return array	 Supported collation
     */
    public static function getSupportedCollateList($dbh)
    {
        static $collates = null;
        if (is_null($collates)) {
            $collates = array();
            $query    = "SHOW COLLATION";

            if (($result = self::mysqli_query($dbh, $query))) {
                while ($row = $result->fetch_assoc()) {
                    $collates[] = $row['Collation'];
                }
                $result->free();
            }
            sort($collates);
        }
        return $collates;
    }

    /**
     * Returns the database names as an array
     *
     * @param obj $dbh			A valid database link handle
     * @param string $dbuser  	An optional dbuser name to search by
     *
     * @return array  A list of all database names
     */
    public static function getDatabases($dbh, $dbuser = '')
    {
        $sql   = strlen($dbuser) ? "SHOW DATABASES LIKE '%".mysqli_real_escape_string($dbh, $dbuser)."%'" : 'SHOW DATABASES';
        $query = self::mysqli_query($dbh, $sql);
        if ($query) {
            while ($db = @mysqli_fetch_array($query)) {
                $all_dbs[] = $db[0];
            }
            if (isset($all_dbs) && is_array($all_dbs)) {
                return $all_dbs;
            }
        }
        return array();
    }

    /**
     * Returns the tables for a database as an array
     *
     * @param obj $dbh   A valid database link handle
     *
     * @return array  A list of all table names
     */
    public static function getTables($dbh)
    {
        $query = self::mysqli_query($dbh, 'SHOW TABLES');
        if ($query) {
            $all_tables = array();

            while ($table = @mysqli_fetch_array($query)) {
                $all_tables[] = $table[0];
            }
            return $all_tables;
        }
        return array();
    }

    /**
     * Get the requested MySQL system variable
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $name  The database variable name to lookup
     *
     * @return string the server variable to query for
     */
    public static function getVariable($dbh, $name)
    {
        $result = self::mysqli_query($dbh, "SHOW VARIABLES LIKE '".mysqli_real_escape_string($dbh, $name)."'");
        $row    = @mysqli_fetch_array($result);
        @mysqli_free_result($result);
        return isset($row[1]) ? $row[1] : null;
    }

    /**
     * Gets the MySQL database version number
     *
     * @param obj    $dbh   A valid database link handle
     * @param bool   $full  True:  Gets the full version
     *                      False: Gets only the numeric portion i.e. 5.5.6 or 10.1.2 (for MariaDB)
     *
     * @return false|string 0 on failure, version number on success
     */
    public static function getVersion($dbh, $full = false)
    {
        if ($full) {
            $version = self::getVariable($dbh, 'version');
        } else {
            $version = preg_replace('/[^0-9.].*/', '', self::getVariable($dbh, 'version'));
        }

        //Fall-back for servers that have restricted SQL for SHOW statement
        //Note: For MariaDB this will report something like 5.5.5 when it is really 10.2.1.
        //This mainly is due to mysqli_get_server_info method which gets the version comment
        //and uses a regex vs getting just the int version of the value.  So while the former
        //code above is much more accurate it may fail in rare situations
        if (empty($version)) {
            $version = mysqli_get_server_info($dbh);
            $version = preg_replace('/[^0-9.].*/', '', $version);
        }

        $version = is_null($version) ? null : $version;
        return empty($version) ? 0 : $version;
    }

    /**
     * Returns a more detailed string about the msyql server version
     * For example on some systems the result is 5.5.5-10.1.21-MariaDB
     * this format is helpful for providing the user a full overview
     *
     * @param conn $dbh Database connection handle
     *
     * @return string The full details of mysql
     */
    public static function getInfo($dbh)
    {
        return mysqli_get_server_info($dbh);
    }

    /**
     * Determine if a MySQL database supports a particular feature
     *
     * @param conn $dbh Database connection handle
     * @param string $feature the feature to check for
     * @return bool
     */
    public static function hasAbility($dbh, $feature)
    {
        $version = self::getVersion($dbh);

        switch (strtolower($feature)) {
            case 'collation' :
            case 'group_concat' :
            case 'subqueries' :
                return version_compare($version, '4.1', '>=');
            case 'set_charset' :
                return version_compare($version, '5.0.7', '>=');
        };
        return false;
    }

    /**
     * Runs a query and returns the results as an array with the column names
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $sql   The sql to run
     *
     * @return array    The result of the query as an array with the column name as the key
     */
    public static function queryColumnToArray($dbh, $sql, $column_index = 0)
    {
        $result_array      = array();
        $full_result_array = self::queryToArray($dbh, $sql);

        for ($i = 0; $i < count($full_result_array); $i++) {
            $result_array[] = $full_result_array[$i][$column_index];
        }
        return $result_array;
    }

    /**
     * Runs a query with no result
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $sql   The sql to run
     *
     * @return array    The result of the query as an array
     */
    public static function queryToArray($dbh, $sql)
    {
        $result = array();

        $query_result = self::mysqli_query($dbh, $sql);

        if ($query_result !== false) {
            if (mysqli_num_rows($query_result) > 0) {
                while ($row = mysqli_fetch_row($query_result)) {
                    $result[] = $row;
                }
            }
        } else {
            $error = mysqli_error($dbh);

            throw new Exception("Error executing query {$sql}.<br/>{$error}");
        }

        return $result;
    }

    /**
     * Runs a query with no result
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $sql   The sql to run
     *
     * @return void
     */
    public static function queryNoReturn($dbh, $sql)
    {
        if (self::mysqli_query($dbh, $sql) === false) {
            $error = mysqli_error($dbh);

            throw new Exception("Error executing query {$sql}.<br/>{$error}");
        }
    }

    /**
     * Drops the table given
     *
     * @param obj    $dbh   A valid database link handle
     * @param string $name	A valid table name to remove
     * 
     * @return null
     */
    public static function dropTable($dbh, $name)
    {
        DUPX_LOG::info('DROP TABLE '.$name, DUPX_Log::LV_DETAILED);
        $escapedName = mysqli_real_escape_string($dbh, $name);
        self::queryNoReturn($dbh, 'DROP TABLE IF EXISTS `'.$escapedName.'`');
    }

    /**
     * Renames an existing table
     *
     * @param obj    $dbh                   A valid database link handle
     * @param string $existing_name         The current tables name
     * @param string $new_name              The new table name to replace the existing name
     * @param string $delete_if_conflict    Delete the table name if there is a conflict
     *
     * @return null
     */
    public static function renameTable($dbh, $existing_name, $new_name, $delete_if_conflict = false)
    {

        if ($delete_if_conflict) {
            self::dropTable($dbh, $new_name);
        }

        DUPX_LOG::info('RENAME TABLE '.$existing_name.' TO '.$new_name, DUPX_Log::LV_DETAILED);
        $escapedOldName = mysqli_real_escape_string($dbh, $existing_name);
        $escapedNewName = mysqli_real_escape_string($dbh, $new_name);

        self::queryNoReturn($dbh, 'RENAME TABLE `'.$escapedOldName.'` TO `'.$escapedNewName.'`');
    }

    /**
     * Renames an existing table
     *
     * @param obj    $dbh                   A valid database link handle
     * @param string $existing_name         The current tables name
     * @param string $new_name              The new table name to replace the existing name
     * @param string $delete_if_conflict    Delete the table name if there is a conflict
     *
     * @return null
     */
    public static function copyTable($dbh, $existing_name, $new_name, $delete_if_conflict = false)
    {
        if ($delete_if_conflict) {
            self::dropTable($dbh, $new_name);
        }

        DUPX_LOG::info('COPY TABLE '.$existing_name.' TO '.$new_name, DUPX_Log::LV_DETAILED);

        $escapedOldName = mysqli_real_escape_string($dbh, $existing_name);
        $escapedNewName = mysqli_real_escape_string($dbh, $new_name);

        self::queryNoReturn($dbh, 'CREATE TABLE `'.$escapedNewName.'` LIKE `'.$escapedOldName.'`');
        self::queryNoReturn($dbh, 'INSERT INTO `'.$escapedNewName.'` SELECT * FROM `'.$escapedOldName.'`');
    }

    /**
     * Sets the MySQL connection's character set.
     *
     * @param resource $dbh     The resource given by mysqli_connect
     * @param string   $charset The character set (optional)
     * @param string   $collate The collation (optional)
     */
    public static function setCharset($dbh, $charset = null, $collate = null)
    {
        $charset = (!isset($charset) ) ? $GLOBALS['DBCHARSET_DEFAULT'] : $charset;
        $collate = (!isset($collate) ) ? '' : $collate;

        if (self::hasAbility($dbh, 'collation') && !empty($charset)) {
            if (function_exists('mysqli_set_charset') && self::hasAbility($dbh, 'set_charset')) {
                if (($result1 = mysqli_set_charset($dbh, mysqli_real_escape_string($dbh, $charset))) === false) {
                    $errMsg = mysqli_error($dbh);
                    DUPX_Log::info('DATABASE ERROR: mysqli_set_charset '.DUPX_Log::varToString($charset).' MSG: '.$errMsg);
                } else {
                    DUPX_Log::info('DATABASE: mysqli_set_charset '.DUPX_Log::varToString($charset), DUPX_Log::LV_DETAILED);
                }

                if (!empty($collate)) {
                    $sql     = "SET collation_connection = ".mysqli_real_escape_string($dbh, $collate);
                    if (($result2 = self::mysqli_query($dbh, $sql)) === false) {
                        $errMsg = mysqli_error($dbh);
                        DUPX_Log::info('DATABASE ERROR: SET collation_connection '.DUPX_Log::varToString($collate).' MSG: '.$errMsg);
                    } else {
                        DUPX_Log::info('DATABASE: SET collation_connection '.DUPX_Log::varToString($collate), DUPX_Log::LV_DETAILED);
                    }
                } else {
                    $result2 = true;
                }

                return $result1 && $result2;
            } else {
                $sql = " SET NAMES ".mysqli_real_escape_string($dbh, $charset);
                if (!empty($collate)) {
                    $sql .= " COLLATE ".mysqli_real_escape_string($dbh, $collate);
                }

                if (($result = self::mysqli_query($dbh, $sql)) === false) {
                    $errMsg = mysqli_error($dbh);
                    DUPX_Log::info('DATABASE SQL ERROR: '.DUPX_Log::varToString($sql).' MSG: '.$errMsg);
                } else {
                    DUPX_Log::info('DATABASE SQL: '.DUPX_Log::varToString($sql), DUPX_Log::LV_DETAILED);
                }

                return $result;
            }
        }
    }

    /**
     * 
     * @param resource $dbh     The resource given by mysqli_connect
     * @return bool|string // return false if current database isent selected or the string name
     * @throws Exception
     */
    public static function getCurrentDatabase($dbh)
    {
        // SELECT DATABASE() as db;
        if (($result = self::mysqli_query($dbh, 'SELECT DATABASE() as db')) === false) {
            return false;
        }
        $assoc = $result->fetch_assoc();
        return isset($assoc['db']) ? $assoc['db'] : false;
    }

    /**
     * mysqli_query wrapper with logging
     *
     * @param mysqli $link
     * @param string $sql
     * @param int $logFailLevel // Write in the log only if the log level is equal to or greater than level
     * @return mysqli_result 
     */
    public static function mysqli_query(\mysqli $link, $query, $logFailLevel = DUPX_Log::LV_DEFAULT, $resultmode = MYSQLI_STORE_RESULT)
    {
        if (($result = mysqli_query($link, $query, $resultmode)) === false) {
            if (DUPX_Log::isLevel($logFailLevel)) {
                $callers  = debug_backtrace();
                $file     = $callers[0]['file'];
                $line     = $callers[0]['line'];
                $queryLog = substr($query, 0, DUPX_Log::isLevel(DUPX_Log::LV_DEBUG) ? 10000 : 500);
                DUPX_Log::info('DB QUERY [ERROR]['.$file.':'.$line.'] MSG: '.mysqli_error($link)."\n\tSQL: ".$queryLog);
                DUPX_Log::info(DUPX_Log::traceToString($callers, 1));
            }
        } else {
            if (DUPX_Log::isLevel(DUPX_Log::LV_HARD_DEBUG)) {
                $callers = debug_backtrace();
                $file    = $callers[0]['file'];
                $line    = $callers[0]['line'];
                DUPX_Log::info('DB QUERY ['.$file.':'.$line.']: '.DUPX_Log::varToString(substr($query, 0, 2000)), DUPX_Log::LV_HARD_DEBUG);
            }
        }
        return $result;
    }
}