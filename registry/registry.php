<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nicolae
 * Date: 06.10.2013
 * Time: 12:12
 * To change this template use File | Settings | File Templates.
 */

class Registry {

    private $objects; // tablou ce va contine obiectele
    private $settings; // tablou ce va contine setarile

    public function __construct() {
        # metoda magica __construct
    }

    public function createAndStoreObjects( $object, $key ) {
        require_once ( $object . '.class.php' );
        $this->objects[ $key ] = new $object( $this );
    }

    public function storeSettings( $setting, $key ) {
        $this->settings[ $key ] = $setting;
    }

    public function getSetting( $key ) {
        return $this->settings[ $key ];
    }

    public function getObjects( $key ) {
        return $this->objects[ $key ];
    }
}