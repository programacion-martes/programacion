<?php
class DB {
    private static $con = null;

    public static function conectar() {
        if (self::$con == null) {
            self::$con = new mysqli("localhost", "root", "", "sistema_ventas");
        }
        return self::$con;
    }
}
?>