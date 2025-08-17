<?php
    require("../Response.php");
    require("../conexion.php");
    session_start();

    $db = new Conexion();
    $sql = "";
    if(!isset($_POST['nombre']) ||   !isset($_POST['montoInicial']) || !isset($_POST['descripcion'])){
        Response::error("Estos campos son obligatorios");
        return;
    }
    $nombre = $_POST['nombre'];
    $monto = $_POST['montoInicial'];
    $descripcion = $_POST['descripcion'];
    $sql = "INSERT INTO caja(usuario_id,monto_inicial,observaciones) VALUES ('$nombre',$monto,'$descripcion');";

    $id = $db->addDato($sql);

    if($id > 0){
        Response::success("", data: $id);
        return;
    }
    Response::error("Ocurrio un error");
?>