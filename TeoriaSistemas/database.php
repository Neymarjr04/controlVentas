<?php
// config/database.php
class Database {
    private $host = "localhost";
    private $database = "sistema_ventas_abarrotes";
    private $username = "root";
    private $password = "";
    private $charset = "utf8";
    private $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            throw new PDOException("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function closeConnection() {
        $this->connection = null;
    }
}

// Función para obtener la conexión globalmente
function getDBConnection() {
    static $database = null;
    if ($database === null) {
        $database = new Database();
    }
    return $database->getConnection();
}

// Configuraciones adicionales
define('SITE_URL', 'http://localhost/sistema-ventas/');
define('ASSETS_URL', SITE_URL . 'assets/');
define('UPLOADS_PATH', __DIR__ . '/../uploads/');
define('UPLOADS_URL', SITE_URL . 'uploads/');

// Configuración de sesión
session_start();

// Función para verificar si el usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

// Función para verificar permisos
function hasPermission($rol_requerido) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $roles_jerarquia = [
        'vendedor' => 1,
        'cajero' => 2,
        'administrador' => 3
    ];
    
    $rol_usuario = $_SESSION['usuario_rol'] ?? '';
    $nivel_usuario = $roles_jerarquia[$rol_usuario] ?? 0;
    $nivel_requerido = $roles_jerarquia[$rol_requerido] ?? 999;
    
    return $nivel_usuario >= $nivel_requerido;
}

// Función para redirigir si no está autorizado
function requireAuth($rol_requerido = 'vendedor') {
    if (!hasPermission($rol_requerido)) {
        header('Location: ' . SITE_URL . 'login.php');
        exit();
    }
}

// Función para formatear moneda
function formatCurrency($amount) {
    return 'S/ ' . number_format($amount, 2);
}

// Función para generar números de venta/compra
function generateNumber($prefix, $table) {
    $db = getDBConnection();
    $year = date('Y');
    $month = date('m');
    
    $sql = "SELECT COUNT(*) as count FROM {$table} WHERE YEAR(fecha) = ? AND MONTH(fecha) = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$year, $month]);
    $count = $stmt->fetch()['count'] + 1;
    
    return $prefix . $year . $month . str_pad($count, 4, '0', STR_PAD_LEFT);
}
?>