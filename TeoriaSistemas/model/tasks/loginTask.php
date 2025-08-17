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
    if( $resultado["estado"] != "1"){
        Response::error("Este usuario esta Inactivo");
    }
    session_start();
    $_SESSION["usuario"] = $resultado["id"];
    $_SESSION["nombre"] = $resultado["nombre"];
    $_SESSION["rol"] = $resultado["rol"];
    Response::success(mensaje:"Datos correctos",data:$resultado["id"]);

?>

