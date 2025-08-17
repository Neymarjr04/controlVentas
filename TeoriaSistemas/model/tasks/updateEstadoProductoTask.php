<?php
    require("../Response.php");
    require("../conexion.php");

    if(!isset($_POST['idProducto']) || !isset($_POST['tipo'])){
        Response::error("El id es obligatorio para la update");
        return;
    }

    $id  = $_POST['idProducto'];
    $tipo = $_POST['tipo'];
    $db = new Conexion();
    $query = "UPDATE productos SET estado = $tipo WHERE id = '$id';";

    $resultado = $db->consulta2($query);

    if($resultado < 0){
        Response::error("Error de consulta");
        return;
    }
    Response::success(mensaje:"Correcto",data:$resultado);

    

?>

