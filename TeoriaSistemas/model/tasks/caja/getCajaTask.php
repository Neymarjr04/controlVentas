<?php
    require("../Response.php");
    require("../conexion.php");
    session_start();

    $db = new Conexion();
    $sql = "";
    if ($_SESSION['rol'] == "administrado") {
        $sql = "SELECT * FROM caja DESC";
    } else {
        $sql = "SELECT * FROM caja
                WHERE estado = 'abierta';";
    }
    $cajas = $db->consulta($sql);

    Response::success("", data: $cajas);
?>