<?php
   class ModeloControlador{

    public static function login(){
        include "pages/login.php";
    }

    public static function productos(){
        include "pages/gestionProd.php";   
    }

    public static function ventas(){
        include "pages/puntodeVenta.php";
    }

    public static function reportes(){
        include "pages/reportes.php";
    }

    public static function sistema(){
        include "pages/sistema.php";
    }

    public static function error_pagina(){
        include "pages/errorPages.php";
    }

    public static function cerrarSecion(){
        include "pages/cerrarSecion.php";
    }

    public static function caja(){
        include "pages/cajaPage.php";
    }

    public static function cajaResults(){
        include "pages/cajaResults.php";
    }

   } 
?>