<?php
// index.php - Router Principal
require_once 'config/database.php';

$page = $_GET['page'] ?? 'login';

// Si no está logueado y no es la página de login, redirigir
if (!isLoggedIn() && $page !== 'login') {
    header('Location: index.php?page=login');
    exit();
}

// Si está logueado y trata de acceder al login, redirigir al dashboard
if (isLoggedIn() && $page === 'login') {
    header('Location: index.php?page=dashboard');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Ventas - Abarrotes La esquinita</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-280px);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }

        .sidebar-header h2 {
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 14px;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section {
            margin-bottom: 10px;
        }

        .menu-section-title {
            padding: 10px 25px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            opacity: 0.6;
            letter-spacing: 1px;
        }

        .menu-item {
            display: block;
            padding: 15px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .menu-item:hover, .menu-item.active {
            background-color: rgba(255,255,255,0.1);
            border-left-color: #3498db;
            transform: translateX(5px);
        }

        .menu-item i {
            margin-right: 12px;
            width: 20px;
        }

        .main-content {
            margin-left: 280px;
            flex: 1;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .top-bar {
            background: white;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-toggle {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #495057;
        }

        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }

        .content-area {
            padding: 25px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #6c757d;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-280px);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content-area {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-store"></i> Abarrotes</h2>
                <p>Don José</p>
            </div>
            <nav class="sidebar-menu">
                <div class="menu-section">
                    <div class="menu-section-title">Principal</div>
                    <a href="index.php?page=dashboard" class="menu-item <?= $page === 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="index.php?page=pos" class="menu-item <?= $page === 'pos' ? 'active' : '' ?>">
                        <i class="fas fa-cash-register"></i> Punto de Venta
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">Gestión</div>
                    <a href="index.php?page=productos" class="menu-item <?= $page === 'productos' ? 'active' : '' ?>">
                        <i class="fas fa-box"></i> Productos
                    </a>
                    <a href="index.php?page=clientes" class="menu-item <?= $page === 'clientes' ? 'active' : '' ?>">
                        <i class="fas fa-users"></i> Clientes
                    </a>
                    <a href="index.php?page=proveedores" class="menu-item <?= $page === 'proveedores' ? 'active' : '' ?>">
                        <i class="fas fa-truck"></i> Proveedores
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">Operaciones</div>
                    <a href="index.php?page=ventas" class="menu-item <?= $page === 'ventas' ? 'active' : '' ?>">
                        <i class="fas fa-receipt"></i> Historial Ventas
                    </a>
                    <a href="index.php?page=compras" class="menu-item <?= $page === 'compras' ? 'active' : '' ?>">
                        <i class="fas fa-shopping-cart"></i> Compras
                    </a>
                    <a href="index.php?page=inventario" class="menu-item <?= $page === 'inventario' ? 'active' : '' ?>">
                        <i class="fas fa-warehouse"></i> Inventario
                    </a>
                    <a href="index.php?page=caja" class="menu-item <?= $page === 'caja' ? 'active' : '' ?>">
                        <i class="fas fa-money-bill-wave"></i> Caja
                    </a>
                </div>
                
                <div class="menu-section">
                    <div class="menu-section-title">Reportes</div>
                    <a href="index.php?page=reportes" class="menu-item <?= $page === 'reportes' ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i> Reportes
                    </a>
                </div>
                
                <?php if (hasPermission('administrador')): ?>
                <div class="menu-section">
                    <div class="menu-section-title">Administración</div>
                    <a href="index.php?page=usuarios" class="menu-item <?= $page === 'usuarios' ? 'active' : '' ?>">
                        <i class="fas fa-user-cog"></i> Usuarios
                    </a>
                    <a href="index.php?page=configuracion" class="menu-item <?= $page === 'configuracion' ? 'active' : '' ?>">
                        <i class="fas fa-cog"></i> Configuración
                    </a>
                </div>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="top-bar-left">
                    <button class="menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 id="pageTitle"><?= ucfirst($page) ?></h1>
                </div>
                <div class="top-bar-right">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?= substr($_SESSION['usuario_nombre'], 0, 1) ?>
                        </div>
                        <div>
                            <div style="font-weight: 600;"><?= $_SESSION['usuario_nombre'] ?></div>
                            <div style="font-size: 12px; color: #6c757d;"><?= ucfirst($_SESSION['usuario_rol']) ?></div>
                        </div>
                    </div>
                    <a href="api/auth.php?action=logout" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Salir
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <?php
                // Incluir la página correspondiente
                switch ($page) {
                    case 'dashboard':
                        include 'pages/dashboard.php';
                        break;
                    case 'pos':
                        include 'pages/pos.php';
                        break;
                    case 'productos':
                        include 'pages/productos.php';
                        break;
                    case 'clientes':
                        include 'pages/clientes.php';
                        break;
                    case 'proveedores':
                        include 'pages/proveedores.php';
                        break;
                    case 'ventas':
                        include 'pages/ventas.php';
                        break;
                    case 'compras':
                        include 'pages/compras.php';
                        break;
                    case 'inventario':
                        include 'pages/inventario.php';
                        break;
                    case 'caja':
                        include 'pages/caja.php';
                        break;
                    case 'reportes':
                        include 'pages/reportes.php';
                        break;
                    case 'usuarios':
                        if (hasPermission('administrador')) {
                            include 'pages/usuarios.php';
                        } else {
                            include 'pages/403.php';
                        }
                        break;
                    case 'configuracion':
                        if (hasPermission('administrador')) {
                            include 'pages/configuracion.php';
                        } else {
                            include 'pages/403.php';
                        }
                        break;
                    default:
                        include 'pages/404.php';
                        break;
                }
                ?>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('hidden');
            mainContent.classList.toggle('expanded');
        }

        // Responsive: ocultar sidebar en móviles
        function handleResize() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.add('hidden');
                mainContent.classList.add('expanded');
            } else {
                sidebar.classList.remove('hidden');
                mainContent.classList.remove('expanded');
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize(); // Ejecutar al cargar
    </script>

    <?php else: ?>
    <!-- Página de Login -->
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div style="background: white; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; width: 100%; max-width: 400px; margin: 20px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center; padding: 30px 20px;">
                <i class="fas fa-store" style="font-size: 3em; margin-bottom: 10px;"></i>
                <h1>Sistema de Ventas</h1>
                <p>Abarrotes Don José</p>
            </div>
            <form method="POST" action="api/auth.php?action=login" style="padding: 40px 30px;">
                <div id="alertContainer"></div>
                
                <?php if (isset($_GET['error'])): ?>
                <div style="padding: 12px 15px; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; font-size: 14px;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
                <?php endif; ?>
                
                <div style="margin-bottom: 25px; position: relative;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Usuario</label>
                    <input type="text" name="usuario" required style="width: 100%; padding: 12px 15px; border: 2px solid #e1e5e9; border-radius: 10px; font-size: 16px;">
                    <i class="fas fa-user" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                </div>
                
                <div style="margin-bottom: 25px; position: relative;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contraseña</label>
                    <input type="password" name="contraseña" required style="width: 100%; padding: 12px 15px; border: 2px solid #e1e5e9; border-radius: 10px; font-size: 16px;">
                    <i class="fas fa-lock" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                </div>
                
                <button type="submit" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
                
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef; font-size: 14px; color: #6c757d; text-align: center;">
                    <strong>Usuarios de prueba:</strong><br>
                    admin / admin123 (Administrador)<br>
                    cajero / cajero123 (Cajero)<br>
                    vendedor / vendedor123 (Vendedor)
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

</body>
</html>