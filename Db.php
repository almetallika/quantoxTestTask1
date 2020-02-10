<?php


namespace App;

include "dbconfig.php";

class Db
{
    static $dbId = false;
    static $mysqlError = '';
    static $mysqlErrorNum = 0;
    static $queryId = false;
    static $showError = true;

    function __construct()
    {
        self::connect(DBUSER, DBPASS, DBNAME, DBHOST, self::showError);
    }

    static function connect($dbUser, $dbPassword, $dbName, $dbLocation = 'localhost', $showError=true)
    {
        self::$dbId = @mysqli_connect($dbLocation, $dbUser, $dbPassword, $dbName);

        if (!self::$dbId) {
            if ($showError) {
                self::display_error(mysqli_connect_error(), '1');
            } else {
                return false;
            }
        }

        $collate = (defined(COLLATE)) ?: 'utf8';
        mysqli_query(self::$dbId, "SET NAMES '" . $collate . "'");
        return true;
    }

    static function query($query, $showError=true)
    {
        if (!self::$dbId) {
            self::connect(DBUSER, DBPASS, DBNAME, DBHOST);
        }

        if (!(self::$queryId = mysqli_query(self::$dbId, $query))) {
            self::$mysqlError = mysqli_error(self::$dbId);
            self::$mysqlErrorNum = mysqli_errno(self::$dbId);
            if($showError) {
                self::display_error(self::$mysqlError, self::$mysqlErrorNum, $query);
            }
        }
        return self::$queryId;
    }

    static function getRow($queryId = '')
    {
        if ($queryId == '') {
            $queryId = self::$queryId;
        }
        return mysqli_fetch_assoc($queryId);
    }

    static function getArray($queryId = '')
    {
        $queryId = ($queryId != '') ?: self::$queryId;
        return mysqli_fetch_array($queryId);
    }

    static function super_query($query, $multi = false)
    {
        if (!$multi) {
            self::query($query);
            $data = self::getRow();
            self::free();
            return $data;
        } else {
            self::query($query);
            $rows = array();
            while ($row = self::getRow()) {
                $rows[] = $row;
            }
            self::free();
            return $rows;
        }
    }

    static function numRows($queryId)
    {
        return mysqli_num_rows($queryId);
    }

    static function insertId()
    {
        return mysqli_insert_id(self::$dbId);
    }

    static function free( $queryId = '' )
    {
        $queryId = ($queryId != '') ?: self::$queryId;
        @mysqli_free_result($queryId);
    }

    static function close()
    {
        @mysqli_close(self::$dbId);
        self::$dbId = false;
    }

    static function display_error($error, $error_num, $query = '')
    {
        header("HTTP/1.0 ".$error_num);
        die($error.' '.$query);
    }
}