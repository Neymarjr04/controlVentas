<?php
require("../Response.php");
require("../conexion.php");

$db = new Conexion();

$ventas    = $db->consulta("SELECT id, fecha, total, metodo_pago FROM ventas");
$detalles  = $db->consulta("SELECT id, venta_id, producto_id, cantidad, precio FROM detalle");
$productos = $db->consulta("SELECT id, nombre, stock, precio, categoria FROM productos");

$reporteVentas = [];
foreach ($ventas as $venta) {
    $fecha = $venta["fecha"];
    if (!isset($reporteVentas[$fecha])) {
        $reporteVentas[$fecha] = ["fecha" => $fecha, "ventas" => 0, "ingresos" => 0];
    }
    $reporteVentas[$fecha]["ventas"] += 1;
    $reporteVentas[$fecha]["ingresos"] += $venta["total"];
}
$reporteVentas = array_values($reporteVentas);

$metodosBase = ["Efectivo", "Tarjeta", "Yape", "Plin"];
$reporteMetodos = [];
$totalVentas = count($ventas);

foreach ($metodosBase as $metodo) {
    $cantidad = 0;
    foreach ($ventas as $venta) {
        if ($venta["metodo_pago"] === $metodo) {
            $cantidad++;
        }
    }
    $porcentaje = $totalVentas > 0 ? ($cantidad / $totalVentas) * 100 : 0;
    $reporteMetodos[] = [
        "metodo" => $metodo,
        "cantidad" => $cantidad,
        "porcentaje" => round($porcentaje, 2)
    ];
}


$productosVendidos = [];
foreach ($detalles as $d) {
    $p = $productos[array_search($d["producto_id"], array_column($productos, "id"))];
    $nombre = $p["nombre"];
    if (!isset($productosVendidos[$nombre])) {
        $productosVendidos[$nombre] = ["nombre" => $nombre, "cantidad" => 0, "ingresos" => 0];
    }
    $productosVendidos[$nombre]["cantidad"] += $d["cantidad"];
    $productosVendidos[$nombre]["ingresos"] += $d["cantidad"] * $d["precio"];
}
usort($productosVendidos, fn($a, $b) => $b["cantidad"] <=> $a["cantidad"]);
$productosVendidos = array_slice(array_values($productosVendidos), 0, 5);


$categoriasData = [];
$totalIngresos = array_sum(array_column($ventas, "total"));

foreach ($detalles as $d) {
    $p = $productos[array_search($d["producto_id"], array_column($productos, "id"))];
    $cat = $p["categoria"];
    if (!isset($categoriasData[$cat])) {
        $categoriasData[$cat] = ["categoria" => $cat, "ventas" => 0, "ingresos" => 0];
    }
    $categoriasData[$cat]["ventas"] += $d["cantidad"];
    $categoriasData[$cat]["ingresos"] += $d["cantidad"] * $d["precio"];
}

foreach ($categoriasData as &$c) {
    $c["porcentaje"] = $totalIngresos > 0 ? round(($c["ingresos"] / $totalIngresos) * 100, 2) : 0;
}
$categoriasData = array_values($categoriasData);

$reportData = [
    "ventas" => $reporteVentas,
    "metodosPago" => $reporteMetodos,
    "productosMasVendidos" => $productosVendidos,
    "categorias" => $categoriasData
];

Response::success("Reporte generado", data: $reportData);
