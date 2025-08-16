<?php
    require_once("controller/controlador.php");
    session_start();

    if (isset($_SESSION['token']) && isset($_SESSION['usuario'])) {

        $valor = $_SERVER["REQUEST_URI"];

        if (isset($_SERVER["REQUEST_URI"]) && $valor != "/" && !isset($_GET['i'])) {

            $valor = ltrim($valor, "/");

            if (method_exists("ModeloControlador", $valor)) {
                ModeloControlador::{$valor}();
            } else {
                ModeloControlador::error_pagina();
            }
        } else {
            ModeloControlador::ventas() ;
        };
    } else {
        ModeloControlador::login();
    }

?>