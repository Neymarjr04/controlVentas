<?php
    require("../../Response.php");
    require("../../conexion.php");
    session_start();

    $db = new Conexion();

    if(!isset($_POST['idCaja'])){
        Response::error("Objeto no valido");
        return;
    }

    $idCaja = $_POST['idCaja'];

    $sql = "UPDATE caja 
            SET estado = 'cerrada' 
            WHERE id = $idCaja";

    $resultado = $db->addDato($sql);

    if ($resultado !== false) {
        Response::success("Caja cerrada correctamente", data: ["id" => $idCaja]);
    } else {
        Response::error("No se pudo cerrar la caja");
    }
?>
