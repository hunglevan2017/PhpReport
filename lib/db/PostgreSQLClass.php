<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//require_once 'lib/define/Conf.php';
/**
 * Description of PostgreSQLClass
 *
 * @author ntvu_1
 */
class PostgreSQLClass {

    var $conf;
    var $confcrc;

    function __construct() {
        $this->conf = new Conf();
        $this->confcrc = new ConfCRC();
    }

    function getConDPO_DIGISOFT($isCRC = false) {
        $connection = null;
        if (!$isCRC) {
            $connection = new PDO("pgsql:host=" . $this->conf->digi_soft_dbhost . ";"
                    . "port=" . $this->conf->digi_soft_dbport . ";"
                    . "dbname=" . $this->conf->digi_soft_dbname, $this->conf->digi_soft_dbuser, $this->conf->digi_soft_dbpass);
            $connection->exec("SET search_path TO " . $this->conf->digi_soft_dbschema . " ;");
            if (!$connection) {
                print("Connection Failed.");exit;
            }
        } else {
            $connection = new PDO("pgsql:host=" . $this->confcrc->digi_soft_dbhost . ";"
                    . "port=" . $this->confcrc->digi_soft_dbport . ";"
                    . "dbname=" . $this->confcrc->digi_soft_dbname, $this->confcrc->digi_soft_dbuser, $this->confcrc->digi_soft_dbpass);
            $connection->exec("SET search_path TO " . $this->confcrc->digi_soft_dbschema . " ;");
            if (!$connection) {
                print("Connection Failed.");exit;
            }
        }
        return $connection;
    }

    public static function getConn() {

        $conf = new Conf();
        $strConn = "host=" . $conf->digi_soft_dbhost . " port=" . $conf->digi_soft_dbport . " dbname=" . $conf->digi_soft_dbname . " user=" . $conf->digi_soft_dbuser . " password=" . $conf->digi_soft_dbpass;
        $connection = pg_connect($strConn);
        pg_query("set search_path to" . $conf->digi_soft_dbschema . ";");
        // let me know if the connection fails
        if (!$connection) {
            print("Connection Failed.");
            exit;
        }
        return $connection;
    }

    public static function getConnectionAnyDB($host,$port,$dbname,$schema,$user,$password) {
        $connection = null;

            $connection = new PDO("pgsql:host=" . $host . ";"
                    . "port=" . $port . ";"
                    . "dbname=" . $dbname, $user, $password);
            $connection->exec("SET search_path TO " . $schema . " ;");
            if (!$connection) {
                print("Connection Failed.");exit;
            }

        return $connection;
    }

}

?>
