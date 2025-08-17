<?php
    require("../Response.php");
    require("../conexion.php");

    $db = new Conexion();

    $query = "SELECT * FROM categorias";

    $resultado = $db->consulta($query);
    if(empty($resultado)){
        Response::error(mensaje:"No se pudo cargas las categorias");
        return;
    }

    Response::success(mensaje:"Categorias Load",data:$resultado);
?>