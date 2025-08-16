<?php
    require("../Response.php");
    require("../conexion.php");
    
    if( !isset($_POST["usuario"]) || !isset($_POST["password"])){
        Response::error(mensaje:"Datos invalidos");
    }

    $usuarios = $_POST["usuario"];
    $contra = $_POST["password"];

    $db = new Conexion();

    $resultado = $db->validador_cuenta($usuarios,$contra);

    if(empty($resultado)){
        Response::error(mensaje:"Esta cuenta no existe");
        return;
    }
    session_start();
    $_SESSION["usuario"] = $resultado[0];
    $_SESSION["nombre"] = $resultado[1];
    $_SESSION["rol"] = $resultado[2];
    Response::success(mensaje:"Datos correctos",data:$resultado[0]);

?>

