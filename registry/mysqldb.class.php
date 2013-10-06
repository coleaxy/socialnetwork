<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nicolae
 * Date: 06.10.2013
 * Time: 12:27
 * To change this template use File | Settings | File Templates.
 */

class Mysqldb {

    private $connections = array(); // permite conectarea mai multor baze de date
    private $activeConnection = 0; // specifica care conexiune este activa
    private $queryCache = array(); // interogari ce au s-au executat si se pastreaza
    private $dataCache = array(); // date ce se pastreaza
    private $queryCounter = 0; // numarul de interogari executate
    private $last; // ultima interogare
    private $registry; // refrinta catre obiectul registry

    public function __construct( Registry $registry ) {
        $this->registry = $registry;
    }

    public function insertData( $table, $data ) {
        $fields = "";
        $values = "";

        foreach ( $data as $f => $v ) {
            $fields .= "`$f`,";
            $values .= ( is_numeric( $v ) && (intval( $v ) == $v) ) ? $v . "," : "'$v',";
        }

        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);

        $insert = "INSERT INTO $table ({$fields}) VALUES ({$values})";
        $this->executeQuery( $insert );
        return true;
    }

    /**
     * igienizarea datelor
     */
    public function sanitizeData( $value ) {
        if ( get_magic_quotes_gpc() ) {
            $value = stripslashes( $value );
        }

        if ( version_compare( phpversion(), "4.3.0" ) == "-1" ) {
            $value = $this->connections[ $this->activeConnection ]->escape_string( $value );
        } else {
            $value = $this->connections[ $this->activeConnection ]->real_escape_string( $value );
        }
        return $value;
    }

    public function getRows() {
        return $this->last->fetch_array(MYSQLI_ASSOC);
    }

    public function numRows() {
        return $this->last->num_rows;
    }

    public function affectedRows() {
        return $this->last->affected_rows;
    }

    public function __destruct() {
        foreach ( $this->connections as $connection ) {
            $connection->close();
        }
    }
}