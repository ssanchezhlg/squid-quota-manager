<?php

require_once('constants.php');

function conexion(){
    $servidor="10.10.10.110";
    $usuario="pquot";
    $password="pquotwebdb";
    $bd="pquot";
    $puerto=881;

    $conexion = new mysqli($servidor, $usuario, $password, $bd, $puerto);

    if ($conexion->connect_error) {
        die("Connection failed: " . $conexion->connect_error);
    }

    $conexion->set_charset("utf8");

    return $conexion;
}
?>
