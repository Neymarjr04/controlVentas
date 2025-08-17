-- Base de datos para Sistema de Ventas de Abarrotes
CREATE DATABASE sistema_ventas_abarrotes;
USE sistema_ventas_abarrotes;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'cajero', 'vendedor') NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL
);

-- Tabla de categorías
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    estado BOOLEAN DEFAULT TRUE
);

-- Tabla de productos
CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_barras VARCHAR(50) UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    categoria_id INT,
    precio_compra DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    stock_actual INT DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    unidad_medida VARCHAR(20) DEFAULT 'unidad',
    estado BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- Tabla de clientes
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    dni_ruc VARCHAR(20) UNIQUE,
    telefono VARCHAR(20),
    direccion TEXT,
    email VARCHAR(100),
    tipo ENUM('natural', 'juridica') DEFAULT 'natural',
    estado BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de proveedores
CREATE TABLE proveedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    ruc VARCHAR(20) UNIQUE NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    direccion TEXT,
    email VARCHAR(100),
    estado BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de caja
CREATE TABLE caja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    fecha_apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP NULL,
    monto_inicial DECIMAL(10,2) NOT NULL,
    monto_final DECIMAL(10,2) NULL,
    estado ENUM('abierta', 'cerrada') DEFAULT 'abierta',
    observaciones TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de ventas
CREATE TABLE ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_venta VARCHAR(20) UNIQUE NOT NULL,
    cliente_id INT,
    usuario_id INT NOT NULL,
    caja_id INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL,
    descuento DECIMAL(10,2) DEFAULT 0,
    impuesto DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'yape', 'plin', 'transferencia') NOT NULL,
    tipo_comprobante ENUM('boleta', 'factura', 'ticket') NOT NULL,
    estado ENUM('completada', 'anulada') DEFAULT 'completada',
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (caja_id) REFERENCES caja(id)
);

-- Tabla detalle de ventas
CREATE TABLE detalle_ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Tabla de compras
CREATE TABLE compras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_compra VARCHAR(20) UNIQUE NOT NULL,
    proveedor_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2) NOT NULL,
    impuesto DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('completada', 'pendiente', 'anulada') DEFAULT 'completada',
    observaciones TEXT,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla detalle de compras
CREATE TABLE detalle_compras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    compra_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Tabla de movimientos de inventario
CREATE TABLE movimientos_inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida') NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(100) NOT NULL,
    referencia_id INT, -- ID de venta o compra
    usuario_id INT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de configuración del sistema
CREATE TABLE configuracion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_empresa VARCHAR(100) NOT NULL,
    ruc_empresa VARCHAR(20) NOT NULL,
    direccion_empresa TEXT NOT NULL,
    telefono_empresa VARCHAR(20),
    email_empresa VARCHAR(100),
    logo_empresa VARCHAR(255),
    igv DECIMAL(5,2) DEFAULT 18.00,
    moneda VARCHAR(10) DEFAULT 'PEN'
);

-- Insertar datos iniciales
INSERT INTO usuarios (nombre, usuario, contraseña, rol) VALUES 
('Administrador', 'admin', 'master123', 'administrador');

INSERT INTO categorias (nombre, descripcion) VALUES 
('Abarrotes', 'Productos básicos de alimentación'),
('Bebidas', 'Bebidas alcohólicas y no alcohólicas'),
('Limpieza', 'Productos de limpieza e higiene'),
('Snacks', 'Golosinas y aperitivos'),
('Lácteos', 'Productos lácteos y derivados');

INSERT INTO configuracion (nombre_empresa, ruc_empresa, direccion_empresa, telefono_empresa, email_empresa) VALUES 
('Abarrotes Don José', '20123456789', 'Av. Principal 123, Lima', '987654321', 'contacto@abarrotes.com');

INSERT INTO productos(codigo_barras,nombre,descripcion,categoria_id,precio_compra,precio_venta,stock_actual,stock_minimo,unidad_medida) VALUES
('750100000001','Arroz Costeño 5kg','Arroz extra calidad en saco de 5kg',1,18.50,22.00,120,20,'kg'),
('750100000002','Aceite Primor 1L','Aceite vegetal refinado botella 1L',1,7.20,9.00,80,15,'L'),
('750100000003','Leche Gloria 400g','Leche evaporada entera en lata',1,3.10,4.00,200,30,'unidad'),
('750100000004','Pan Bimbo Blanco','Pan de molde blanco 500g',1,4.00,5.50,60,10,'unidad'),
('750100000005','Detergente Ariel 1kg','Detergente en polvo para ropa',2,10.50,13.00,90,15,'kg'),
('750100000006','Shampoo Sedal 750ml','Shampoo hidratante cabello normal',2,11.00,14.50,70,10,'L'),
('750100000007','Jabón Dove 90g','Jabón de tocador humectante',2,2.20,3.00,150,25,'unidad'),
('750100000008','Cereal Zucaritas 300g','Cereal de maíz azucarado',1,6.50,8.50,50,10,'unidad'),
('750100000009','Azúcar Rubia 1kg','Azúcar rubia en bolsa 1kg',1,4.20,5.20,110,20,'kg'),
('750100000010','Laptop Lenovo i5','Laptop 14" Intel Core i5, 8GB RAM, 256GB SSD',3,2100.00,2600.00,10,2,'unidad'),
('750100000011','Mouse Logitech M170','Mouse inalámbrico óptico USB',3,40.00,55.00,35,5,'unidad'),
('750100000012','Teclado Redragon K552','Teclado mecánico retroiluminado',3,95.00,120.00,25,5,'unidad'),
('750100000013','Celular Samsung A14','Smartphone 128GB, 4GB RAM',3,650.00,820.00,15,3,'unidad'),
('750100000014','Cerveza Cusqueña 310ml','Botella de cerveza dorada',4,3.50,5.00,200,40,'unidad'),
('750100000015','Vino Tabernero Borgoña 750ml','Vino de mesa borgoña tinto',4,18.00,25.00,30,5,'L'),
('750100000016','Agua San Luis 625ml','Agua mineral sin gas',4,1.20,1.80,250,50,'unidad'),
('750100000017','Cuaderno Stanford A4','Cuaderno cuadriculado 100 hojas',5,6.50,9.00,80,15,'unidad'),
('750100000018','Lapicero Pilot Azul','Lapicero tinta azul 0.7mm',5,1.50,2.20,300,50,'unidad'),
('750100000019','Resaltador Stabilo Amarillo','Resaltador fluorescente amarillo',5,2.80,4.00,120,20,'unidad'),
('750100000020','Tijera Maped 6"','Tijera escolar de acero inoxidable',5,3.50,5.50,60,10,'unidad');
