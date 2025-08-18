<?php
    
    $ruta = $_GET['idCaja'];
    $direccion = "caja";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> CAJA | Response </title>
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="./styles/gestionProd.css" />
    <link rel="stylesheet" href="./styles/navbar.css">
    <link rel="stylesheet" href="./styles/caja.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="./js/index.js"></script>
    <script defer src="/js/cajaResult.js"></script>

    </head>

    <body>
    <?php include "components/aside.php"; ?>
    <?php
    if ($_SESSION['rol'] != "administrador") {
    ?>
        <p>No tienes permisos para acceder aqui</p>
    <?php
        return;
    }?>
    <div id="clave" data-id="<?php echo $ruta;?>" class="container" >

    </div>

</body>
</html>