<?php
// classes/Usuario.php
class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function login($usuario, $contraseña) {
        $sql = "SELECT id, nombre, usuario, contraseña, rol FROM usuarios WHERE usuario = ? AND estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$usuario]);
        
        if ($user = $stmt->fetch()) {
            if (password_verify($contraseña, $user['contraseña'])) {
                // Actualizar último acceso
                $this->updateLastAccess($user['id']);
                
                // Guardar en sesión
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_rol'] = $user['rol'];
                
                return true;
            }
        }
        return false;
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    private function updateLastAccess($userId) {
        $sql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
    }
    
    public function create($datos) {
        $sql = "INSERT INTO usuarios (nombre, usuario, contraseña, rol) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $contraseñaHash = password_hash($datos['contraseña'], PASSWORD_DEFAULT);
        return $stmt->execute([
            $datos['nombre'],
            $datos['usuario'],
            $contraseñaHash,
            $datos['rol']
        ]);
    }
    
    public function getAll() {
        $sql = "SELECT id, nombre, usuario, rol, estado, fecha_registro, ultimo_acceso FROM usuarios ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT id, nombre, usuario, rol, estado FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function update($id, $datos) {
        $sql = "UPDATE usuarios SET nombre = ?, usuario = ?, rol = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['usuario'],
            $datos['rol'],
            $id
        ]);
    }
    
    public function delete($id) {
        $sql = "UPDATE usuarios SET estado = 0 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}

// classes/Producto.php
class Producto {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function create($datos) {
        $sql = "INSERT INTO productos (codigo_barras, nombre, descripcion, categoria_id, precio_compra, precio_venta, stock_actual, stock_minimo, unidad_medida) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['codigo_barras'],
            $datos['nombre'],
            $datos['descripcion'],
            $datos['categoria_id'],
            $datos['precio_compra'],
            $datos['precio_venta'],
            $datos['stock_actual'] ?? 0,
            $datos['stock_minimo'] ?? 5,
            $datos['unidad_medida'] ?? 'unidad'
        ]);
    }
    
    public function getAll() {
        $sql = "SELECT p.*, c.nombre as categoria_nombre FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.estado = 1 ORDER BY p.nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.id = ? AND p.estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByBarcode($codigo) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.codigo_barras = ? AND p.estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$codigo]);
        return $stmt->fetch();
    }
    
    public function search($termino) {
        $sql = "SELECT p.*, c.nombre as categoria_nombre FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id 
                WHERE (p.nombre LIKE ? OR p.codigo_barras LIKE ?) AND p.estado = 1 
                ORDER BY p.nombre LIMIT 20";
        $termino = "%{$termino}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$termino, $termino]);
        return $stmt->fetchAll();
    }
    
    public function updateStock($id, $cantidad, $operacion = 'suma') {
        if ($operacion === 'suma') {
            $sql = "UPDATE productos SET stock_actual = stock_actual + ? WHERE id = ?";
        } else {
            $sql = "UPDATE productos SET stock_actual = stock_actual - ? WHERE id = ?";
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$cantidad, $id]);
    }
    
    public function getStockBajo() {
        $sql = "SELECT * FROM productos WHERE stock_actual <= stock_minimo AND estado = 1 ORDER BY stock_actual";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function update($id, $datos) {
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, categoria_id = ?, precio_compra = ?, precio_venta = ?, stock_minimo = ?, unidad_medida = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['descripcion'],
            $datos['categoria_id'],
            $datos['precio_compra'],
            $datos['precio_venta'],
            $datos['stock_minimo'],
            $datos['unidad_medida'],
            $id
        ]);
    }
}

// classes/Cliente.php
class Cliente {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function create($datos) {
        $sql = "INSERT INTO clientes (nombre, dni_ruc, telefono, direccion, email, tipo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['dni_ruc'],
            $datos['telefono'] ?? null,
            $datos['direccion'] ?? null,
            $datos['email'] ?? null,
            $datos['tipo'] ?? 'natural'
        ]);
    }
    
    public function getAll() {
        $sql = "SELECT * FROM clientes WHERE estado = 1 ORDER BY nombre";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM clientes WHERE id = ? AND estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function search($termino) {
        $sql = "SELECT * FROM clientes WHERE (nombre LIKE ? OR dni_ruc LIKE ?) AND estado = 1 ORDER BY nombre LIMIT 20";
        $termino = "%{$termino}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$termino, $termino]);
        return $stmt->fetchAll();
    }
    
    public function update($id, $datos) {
        $sql = "UPDATE clientes SET nombre = ?, dni_ruc = ?, telefono = ?, direccion = ?, email = ?, tipo = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['dni_ruc'],
            $datos['telefono'],
            $datos['direccion'],
            $datos['email'],
            $datos['tipo'],
            $id
        ]);
    }
}

// classes/Venta.php
class Venta {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function create($datos) {
        try {
            $this->db->beginTransaction();
            
            // Crear venta
            $numeroVenta = generateNumber('V', 'ventas');
            $sql = "INSERT INTO ventas (numero_venta, cliente_id, usuario_id, caja_id, subtotal, descuento, impuesto, total, metodo_pago, tipo_comprobante) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $numeroVenta,
                $datos['cliente_id'] ?? null,
                $_SESSION['usuario_id'],
                $datos['caja_id'],
                $datos['subtotal'],
                $datos['descuento'] ?? 0,
                $datos['impuesto'] ?? 0,
                $datos['total'],
                $datos['metodo_pago'],
                $datos['tipo_comprobante']
            ]);
            
            $ventaId = $this->db->lastInsertId();
            
            // Crear detalle de venta
            foreach ($datos['productos'] as $producto) {
                $sql = "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    $ventaId,
                    $producto['id'],
                    $producto['cantidad'],
                    $producto['precio_unitario'],
                    $producto['subtotal']
                ]);
                
                // Actualizar stock
                $productoObj = new Producto();
                $productoObj->updateStock($producto['id'], $producto['cantidad'], 'resta');
                
                // Registrar movimiento de inventario
                $this->registrarMovimientoInventario($producto['id'], $producto['cantidad'], 'salida', 'Venta #' . $numeroVenta, $ventaId);
            }
            
            $this->db->commit();
            return $ventaId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    private function registrarMovimientoInventario($productoId, $cantidad, $tipo, $motivo, $referenciaId) {
        $sql = "INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, motivo, referencia_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productoId, $tipo, $cantidad, $motivo, $referenciaId, $_SESSION['usuario_id']]);
    }
    
    public function getVentasHoy() {
        $sql = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha) = CURDATE() AND estado = 'completada'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }
    
    public function getIngresosHoy() {
        $sql = "SELECT COALESCE(SUM(total), 0) as total FROM ventas WHERE DATE(fecha) = CURDATE() AND estado = 'completada'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }
    
    public function getVentasPorPeriodo($fechaInicio, $fechaFin) {
        $sql = "SELECT v.*, c.nombre as cliente_nombre, u.nombre as usuario_nombre 
                FROM ventas v 
                LEFT JOIN clientes c ON v.cliente_id = c.id 
                LEFT JOIN usuarios u ON v.usuario_id = u.id 
                WHERE DATE(v.fecha) BETWEEN ? AND ? AND v.estado = 'completada' 
                ORDER BY v.fecha DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fechaInicio, $fechaFin]);
        return $stmt->fetchAll();
    }
    
    public function getDetalleVenta($ventaId) {
        $sql = "SELECT dv.*, p.nombre as producto_nombre 
                FROM detalle_ventas dv 
                LEFT JOIN productos p ON dv.producto_id = p.id 
                WHERE dv.venta_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$ventaId]);
        return $stmt->fetchAll();
    }
}

// classes/Caja.php
class Caja {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function abrirCaja($montoInicial) {
        // Verificar si hay caja abierta
        if ($this->getCajaAbierta()) {
            throw new Exception("Ya hay una caja abierta para este usuario");
        }
        
        $sql = "INSERT INTO caja (usuario_id, monto_inicial) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$_SESSION['usuario_id'], $montoInicial]);
    }
    
    public function cerrarCaja($montoFinal, $observaciones = '') {
        $cajaAbierta = $this->getCajaAbierta();
        if (!$cajaAbierta) {
            throw new Exception("No hay caja abierta para cerrar");
        }
        
        $sql = "UPDATE caja SET fecha_cierre = NOW(), monto_final = ?, estado = 'cerrada', observaciones = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$montoFinal, $observaciones, $cajaAbierta['id']]);
    }
    
    public function getCajaAbierta() {
        $sql = "SELECT * FROM caja WHERE usuario_id = ? AND estado = 'abierta' ORDER BY fecha_apertura DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$_SESSION['usuario_id']]);
        return $stmt->fetch();
    }
    
    public function getResumenCaja($cajaId) {
        // Obtener ventas de la caja
        $sql = "SELECT COUNT(*) as total_ventas, COALESCE(SUM(total), 0) as total_ingresos 
                FROM ventas WHERE caja_id = ? AND estado = 'completada'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cajaId]);
        $resumen = $stmt->fetch();
        
        // Obtener información de la caja
        $sql = "SELECT * FROM caja WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cajaId]);
        $caja = $stmt->fetch();
        
        return array_merge($resumen, $caja);
    }
}

// classes/Reporte.php
class Reporte {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function getProductosMasVendidos($limite = 10, $fechaInicio = null, $fechaFin = null) {
        $whereClause = "WHERE v.estado = 'completada'";
        $params = [];
        
        if ($fechaInicio && $fechaFin) {
            $whereClause .= " AND DATE(v.fecha) BETWEEN ? AND ?";
            $params = [$fechaInicio, $fechaFin];
        }
        
        $sql = "SELECT p.nombre, SUM(dv.cantidad) as total_vendido, SUM(dv.subtotal) as total_ingresos
                FROM detalle_ventas dv 
                INNER JOIN productos p ON dv.producto_id = p.id 
                INNER JOIN ventas v ON dv.venta_id = v.id 
                {$whereClause}
                GROUP BY dv.producto_id, p.nombre 
                ORDER BY total_vendido DESC 
                LIMIT ?";
        
        $params[] = $limite;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getVentasPorDia($fechaInicio, $fechaFin) {
        $sql = "SELECT DATE(fecha) as fecha, COUNT(*) as total_ventas, SUM(total) as total_ingresos 
                FROM ventas 
                WHERE DATE(fecha) BETWEEN ? AND ? AND estado = 'completada' 
                GROUP BY DATE(fecha) 
                ORDER BY fecha";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$fechaInicio, $fechaFin]);
        return $stmt->fetchAll();
    }
    
    public function getEstadisticasGenerales() {
        $stats = [];
        
        // Total productos
        $sql = "SELECT COUNT(*) as total FROM productos WHERE estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total_productos'] = $stmt->fetch()['total'];
        
        // Total clientes
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE estado = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total_clientes'] = $stmt->fetch()['total'];
        
        // Ventas hoy
        $venta = new Venta();
        $stats['ventas_hoy'] = $venta->getVentasHoy();
        $stats['ingresos_hoy'] = $venta->getIngresosHoy();
        
        // Productos con stock bajo
        $producto = new Producto();
        $stats['productos_stock_bajo'] = count($producto->getStockBajo());
        
        return $stats;
    }
}
?>