<?php
    require("../../Response.php");
    require("../../conexion.php");
    session_start();

    $db = new Conexion();
    if(!isset($_POST['nombre']) ||   !isset($_POST['montoInicial']) || !isset($_POST['descripcion'])){
        Response::error("Estos campos son obligatorios");
        return;
    }
    $nombre = $_POST['nombre'];
    $id = $_SESSION['usuario'];
    $monto = $_POST['montoInicial'];
    $descripcion = $_POST['descripcion'];
    $sql = "INSERT INTO caja(usuario_id,nombre,monto_inicial,observaciones) VALUES ($id,'$nombre',$monto,'$descripcion');";

    $id = $db->addDato($sql);

    if($id > 0){
        $respuesta = $db->consulta("SELECT * FROM caja WHERE id = $id");
        Response::success("", data: $respuesta);
        return;
    }
    Response::error("Ocurrio un error");
?>