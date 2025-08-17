<?php
require("../Response.php");
require("../conexion.php");

if(
    !isset($_POST['nombre']) || 
    !isset($_POST['precio_compra']) || 
    !isset($_POST['precio_venta']) || 
    !isset($_POST['categoria_id'])
){
    Response::error("Faltan campos obligatorios: nombre, precio_compra, precio_venta, categoria_id");
    return;
}

$nombre        = $_POST['nombre'];
$descripcion   = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
$categoria_id  = intval($_POST['categoria_id']);
$precio_compra = floatval($_POST['precio_compra']);
$precio_venta  = floatval($_POST['precio_venta']);
$stock_actual  = isset($_POST['stock_actual']) ? intval($_POST['stock_actual']) : 0;
$stock_minimo  = isset($_POST['stock_minimo']) ? intval($_POST['stock_minimo']) : 5;
$unidad        = isset($_POST['unidad_medida']) ? $_POST['unidad_medida'] : "unidad";
$codigo_barras = isset($_POST['codigo_barras']) ? $_POST['codigo_barras'] : null;

$db = new Conexion();

$query = "
    INSERT INTO productos 
    (codigo_barras, nombre, descripcion, categoria_id, precio_compra, precio_venta, stock_actual, stock_minimo, unidad_medida) 
    VALUES 
    ('$codigo_barras', '$nombre', '$descripcion', $categoria_id, $precio_compra, $precio_venta, $stock_actual, $stock_minimo, '$unidad');
";

$resultado = $db->consulta2($query);

if($resultado <= 0){
    Response::error("Error al insertar el producto");
    return;
}

Response::success(mensaje: "Producto registrado correctamente", data: $resultado);
