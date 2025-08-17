<?php
require("../Response.php");
require("../conexion.php");


$db = new Conexion();
$queryProductos = "productos.id,productos.id,productos.codigo_barras,productos.nombre,productos.precio_compra,productos.precio_venta,productos.stock_actual,productos.stock_minimo,productos.unidad_medida,productos.estado,productos.fecha_registro";
$query = "SELECT productos.*,categorias.nombre as categoria_nombre FROM productos INNER JOIN  categorias ON productos.categoria_id = categorias.id;";

$resultado = $db->consulta($query);

if (empty($resultado)) {
    Response::error(mensaje: "Ocurrio un error");
    return;
}

Response::success(mensaje: "correcto", data: $resultado);
