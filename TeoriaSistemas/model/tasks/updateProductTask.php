<?php
require("../Response.php");
require("../conexion.php");

if (!isset($_POST['idProducto'])) {
    Response::error("El idProducto es obligatorio para actualizar");
    return;
}

$id = intval($_POST['idProducto']);

$campos = [];

if (isset($_POST['nombre']))        $campos[] = "nombre = '" . $_POST['nombre'] . "'";
if (isset($_POST['descripcion']))   $campos[] = "descripcion = '" . $_POST['descripcion'] . "'";
if (isset($_POST['categoria_id']))  $campos[] = "categoria_id = " . intval($_POST['categoria_id']);
if (isset($_POST['precio_compra'])) $campos[] = "precio_compra = " . floatval($_POST['precio_compra']);
if (isset($_POST['precio_venta']))  $campos[] = "precio_venta = " . floatval($_POST['precio_venta']);
if (isset($_POST['stock_actual']))  $campos[] = "stock_actual = " . intval($_POST['stock_actual']);
if (isset($_POST['stock_minimo']))  $campos[] = "stock_minimo = " . intval($_POST['stock_minimo']);
if (isset($_POST['unidad_medida'])) $campos[] = "unidad_medida = '" . $_POST['unidad_medida'] . "'";
if (isset($_POST['estado']))        $campos[] = "estado = " . intval($_POST['estado']);

if (count($campos) === 0) {
    Response::error("No se enviaron campos para actualizar");
    return;
}

$db = new Conexion();

$query = "UPDATE productos SET " . implode(", ", $campos) . " WHERE id = $id;";

$resultado = $db->consulta2($query);

if ($resultado <= 0) {
    Response::error("No se pudo actualizar el producto o no hubo cambios");
    return;
}

Response::success(mensaje: "Producto actualizado correctamente", data: $resultado);
