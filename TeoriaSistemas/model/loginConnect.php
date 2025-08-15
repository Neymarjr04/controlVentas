<?php
  require_once( 'model/conexion.php' );

  if( !isset($_POST['usuario']) || !isset($_POST['password']) ) {
    return "Acceso denegado";
  }

  $usuario = $_POST['usuario'];
  $password = $_POST['password'];

  $conexion = new Conexion();
  $db = $conexion->getConexion();

  try {
    $stmt = $db->prepare("SELECT * FROM usuarios WHERE usuario = :usuario AND password = :password");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    print_r($user);
  } catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
    exit;
  }

?>