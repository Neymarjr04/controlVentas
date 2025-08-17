<?php
    require("../Response.php");
    require("../conexion.php");

    if( !isset($_POST['idProduct']) || !isset( $_POST['cantidad'])){
        Response::error(" Datos importantes no recibidos ");
        return;
    }

    $cantidad = $_POST["cantidad"];
    $id = $_POST['idProduct'];
    $db = new Conexion();

    $query = "UPDATE productos SET stock_actual = $cantidad WHERE id=$id;";

    $resultado = $db->consulta2($query);

    if($resultado === -1){
        Response::error("Ocurrio un error al ingresar el producto");
        return;
    }

    Response::success("Se actualizo los datos");

?>