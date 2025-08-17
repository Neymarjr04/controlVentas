<?php
require("../Response.php");
require("../conexion.php");
session_start();

$payload = json_decode(file_get_contents("php://input"), true);
if (!$payload) $payload = $_POST;

if (!isset($payload["productos"]) || !is_array($payload["productos"])) {
    Response::error("Faltan productos");
    return;
}

$db = new Conexion();

$productos = $payload["productos"];
$cliente_id = isset($payload["cliente_id"]) ? intval($payload["cliente_id"]) : "NULL";
$usuario_id = $_SESSION['usuario'];
$metodo_pago = isset($payload["metodo_pago"]) ? strtolower($payload["metodo_pago"]) : "";
$tipo_comprobante = isset($payload["tipo_comprobante"]) ? strtolower($payload["tipo_comprobante"]) : "ticket";
$descuento = isset($payload["descuento"]) ? floatval($payload["descuento"]) : 0;

$db->consulta2("");

$caja_id = isset($payload["caja_id"]) ? intval($payload["caja_id"]) : 0;

if (!$usuario_id || !$caja_id || !$metodo_pago) {
    Response::error("usuario_id, caja_id y metodo_pago son obligatorios");
    return;
}

$permitidos_pago = ["efectivo","tarjeta","yape","plin","transferencia"];
$permitidos_comp = ["boleta","factura","ticket"];
if (!in_array($metodo_pago, $permitidos_pago) || !in_array($tipo_comprobante, $permitidos_comp)) {
    Response::error("Método de pago o comprobante inválido");
    return;
}


$igv = floatval($db->consulta2("SELECT igv FROM configuracion ORDER BY id DESC LIMIT 1"));
if ($igv <= 0) $igv = 18.00;

$subtotal_calc = 0.0;
foreach ($productos as $p) {
    $qty = intval($p["quantity"] ?? 0);
    $price = floatval($p["precio_venta"] ?? 0);
    if ($qty <= 0 || $price < 0) {
        Response::error("Producto inválido en la lista");
        return;
    }
    $subtotal_calc += ($price * $qty);
}

if ($descuento < 0) $descuento = 0;
$impuesto = round($subtotal_calc * ($igv/100), 2);
$total = round($subtotal_calc - $descuento + $impuesto, 2);

$venta_seq = intval($db->consulta2("SELECT COALESCE(MAX(id)+1,1) FROM ventas"));
$numero_venta = "V".date("Ymd").str_pad(strval($venta_seq), 6, "0", STR_PAD_LEFT);

$caja_ok = intval($db->consulta2("SELECT COUNT(*) FROM caja WHERE id = $caja_id AND estado='abierta'"));
if ($caja_ok <= 0) {
    Response::error("La caja no está abierta");
    return;
}

$db->consulta2("START TRANSACTION");

$cli_val = ($cliente_id === "NULL") ? "NULL" : $cliente_id;
$qVenta = "
INSERT INTO ventas
(numero_venta, cliente_id, usuario_id, caja_id, subtotal, descuento, impuesto, total, metodo_pago, tipo_comprobante, estado)
VALUES
('$numero_venta', $cli_val, $usuario_id, $caja_id, $subtotal_calc, $descuento, $impuesto, $total, '$metodo_pago', '$tipo_comprobante', 'completada')
";
$rVenta = $db->consulta2($qVenta);
if ($rVenta <= 0) {
    $db->consulta2("ROLLBACK");
    Response::error("No se pudo registrar la venta");
    return;
}

$venta_id = intval($db->consulta2("SELECT LAST_INSERT_ID()"));

$qDet = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES ";
$values = [];
foreach ($productos as $p) {
    $pid = intval($p["id"]);
    $qty = intval($p["quantity"]);
    $price = floatval($p["precio_venta"]);
    $sub = round($price * $qty, 2);

    $affected = intval($db->consulta2("UPDATE productos SET stock_actual = stock_actual - $qty WHERE id = $pid AND stock_actual >= $qty"));
    if ($affected <= 0) {
        $db->consulta2("ROLLBACK");
        Response::error("Stock insuficiente para el producto ID $pid");
        return;
    }

    $values[] = "($venta_id, $pid, $qty, $price, $sub)";

    $db->consulta2("
        INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, motivo, referencia_id, usuario_id)
        VALUES ($pid, 'salida', $qty, 'venta', $venta_id, $usuario_id)
    ");
}

if (count($values) > 0) {
    $rDet = $db->consulta2($qDet . implode(",", $values));
    if ($rDet <= 0) {
        $db->consulta2("ROLLBACK");
        Response::error("No se pudo registrar el detalle de la venta");
        return;
    }
}

$db->consulta2("COMMIT");

Response::success(
    mensaje: "Venta registrada",
    data: [
        "venta_id" => $venta_id,
        "numero_venta" => $numero_venta,
        "subtotal" => round($subtotal_calc, 2),
        "descuento" => round($descuento, 2),
        "impuesto" => $impuesto,
        "total" => $total,
        "igv_porcentaje" => $igv
    ]
);
