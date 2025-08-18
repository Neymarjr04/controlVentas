<?php
    require("../../Response.php");
    require("../../conexion.php");
    session_start();

    $db = new Conexion();

    if(!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador'){
        Response::error("No tienes permisos para ver los resultado");
        return;
    }

    if(!isset($_POST['caja_id'])){
        Response::error("El id es Importante");
        return;
    }

    $cajaId = $_POST['caja_id'];

    
    $sql = "SELECT v.id, v.fecha, v.total, v.metodo_pago, u.nombre as usuario
            FROM ventas v
            INNER JOIN usuarios u ON v.usuario_id = u.id
            WHERE v.caja_id = $cajaId
            ORDER BY v.fecha DESC;";

    $response = $db->consulta($sql);

    if(!$response){
        Response::error("Error al ver las ventas");
        return;
    }

    Response::success("Ventas obtenidas correctamente", data: $response);
?>
