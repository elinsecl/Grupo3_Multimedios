<?php

class Conexion {


    public static function conectar(){

        $host = "srv863.hstgr.io";
        $db = "u484426513_ms225";
        $user = "u484426513_ms225";
        $pass = "k&9W>hnC/X";
        $charset = "utf8mb4";

        $dns = "mysql:host=$host;dbname=$db;charset=$charset";

        try {

            $pdo = new PDO($dns, $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        }catch( PDOException $errores ){
            die("Error al conectar: ". $errores->getMessage() );

        }
    }

}