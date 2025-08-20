<?php
require("../Response.php");
require("../conexion.php");
session_start();

if (!isset($_POST['datos']) || !is_array($_POST['datos'])) {
    Response::error("Faltan productos");
    return;
}

$db = new Conexion();

$productos        = $_POST['datos'];
$cliente_id       = isset($_POST["cliente_id"]) ? intval($_POST["cliente_id"]) : null; // NULL real en SQL
$usuario_id       = isset($_SESSION['usuario']) ? intval($_SESSION['usuario']) : 0;
$metodo_pago      = isset($_POST["metodo_pago"]) ? strtolower(trim($_POST["metodo_pago"])) : "efectivo";
$tipo_comprobante = isset($_POST["tipo_comprobante"]) ? strtolower(trim($_POST["tipo_comprobante"])) : "ticket";
$descuento        = isset($_POST["descuento"]) ? floatval($_POST["descuento"]) : 0.0;
$caja_id          = isset($_POST["caja"]) ? intval($_POST["caja"]) : 0;

if ($usuario_id <= 0 || $caja_id <= 0 || $metodo_pago === "") {
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
    $pid   = intval($p["id"] ?? 0);
    $qty   = intval($p["quantity"] ?? 0);
    $price = floatval($p["precio_venta"] ?? 0);

    if ($pid <= 0 || $qty <= 0 || $price < 0) {
        Response::error("Producto inválido en la lista");
        return;
    }
    $subtotal_calc += ($price * $qty);
}

$descuento = max(0.0, $descuento);
$impuesto  = round($subtotal_calc * ($igv / 100), 2);
$total     = round($subtotal_calc - $descuento + $impuesto, 2);

$caja_ok = intval($db->consulta2("SELECT COUNT(*) FROM caja WHERE id = $caja_id AND estado='abierta'"));
if ($caja_ok <= 0) {
    Response::error("La caja no está abierta");
    return;
}

$venta_seq    = intval($db->consulta2("SELECT COALESCE(MAX(id)+1,1) FROM ventas"));
$numero_venta = "V" . date("Ymd") . str_pad((string)$venta_seq, 6, "0", STR_PAD_LEFT);


$cli_sql = is_null($cliente_id) ? "NULL" : $cliente_id;
$qVenta  = "
INSERT INTO ventas
(numero_venta, cliente_id, usuario_id, caja_id, subtotal, descuento, impuesto, total, metodo_pago, tipo_comprobante, estado)
VALUES
('$numero_venta', $cli_sql, $usuario_id, $caja_id, $subtotal_calc, $descuento, $impuesto, $total, '$metodo_pago', '$tipo_comprobante', 'completada')
";

$rVenta = $db->addDato($qVenta);

$venta_id = $rVenta;

$values = [];

foreach ($productos as $p) {
    $pid   = intval($p["id"]);
    $qty   = intval($p["quantity"]);
    $price = floatval($p["precio_venta"]);
    $sub   = round($price * $qty, 2);

    $stock_actual = intval($db->consulta2("SELECT stock_actual FROM productos WHERE id = $pid"));
    if ($stock_actual < $qty) {
        $db->consulta2("ROLLBACK");
        Response::error("Stock insuficiente para el producto ID $pid");
        return;
    }

    $rUpd = $db->addDato("UPDATE productos SET stock_actual = stock_actual - $qty WHERE id = $pid AND stock_actual >= $qty");
    

    $values[] = "($venta_id, $pid, $qty, $price, $sub)";

    $rMov = $db->addDato("
        INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, motivo, referencia_id, usuario_id)
        VALUES ($pid, 'salida', $qty, 'venta', $venta_id, $usuario_id)
    ");
    if ($rMov === false || $rMov <= 0) {
        Response::error("No se pudo registrar el movimiento de inventario para el producto ID $pid");
        return;
    }
}

if (!empty($values)) {
    $qDet = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES " . implode(",", $values);
    $rDet = $db->addDato($qDet);
    if ($rDet === false || $rDet <= 0) {
        Response::error("No se pudo registrar el detalle de la venta");
        return;
    }
}


Response::success(
    mensaje: "Venta registrada",
    data: [
        "venta_id"       => $venta_id,
        "numero_venta"   => $numero_venta,
        "subtotal"       => round($subtotal_calc, 2),
        "descuento"      => round($descuento, 2),
        "impuesto"       => $impuesto,
        "total"          => $total,
        "igv_porcentaje" => $igv
    ]
);
