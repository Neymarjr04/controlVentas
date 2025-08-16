
<?php

  class Conexion{
    private $host = "localhost";
    private $user = "yukio";
    private $password = "master123";
    private $database = "sistema_ventas_abarrotes";
    private $sql = "";
    public $conexion;

    public function __construct(){

      try {
        $conexionString = "mysql:host=$this->host;dbname=$this->database";
        $this->conexion = new PDO($conexionString, $this->user, $this->password);
        //$this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
      }
      
    }

    public function getConexion(){
      return $this->conexion;
    }

    public function consulta($consulta){
      try {
          $this->sql = $consulta;
          $resultado = $this->conexion->prepare($this->sql);
          $resultado->execute();
          return $resultado->fetchAll(PDO::FETCH_COLUMN);
      } catch (PDOException $e) {
          return -1;
      }
    }

    public function validador_cuenta($dni,$contra){
        $this->sql = "select id from usuarios where usuario = '$dni' AND contraseña='$contra';";
        $resultado = $this->conexion->prepare($this->sql);
        $resultado->execute();
        return $resultado->fetchAll(PDO::FETCH_COLUMN);
    }
  }

?>