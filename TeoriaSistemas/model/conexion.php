
<?php

  class Conexion{
    private $host = "localhost";
    private $user = "root";
    private $password = "";
    private $database = "sistema_ventas_abarrotes";
    public $conexion;

    public function __construct(){

      try {
        $conexionString = "mysql:host=$this->host;dbname=$this->database;charset=utf8";
        $this->conexion = new PDO($conexionString, $this->user, $this->password);
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
      }
      
    }

    public function getConexion(){
      return $this->conexion;
    }

    public function closeConexion(){
      $this->conexion->close();
    }
  }

?>