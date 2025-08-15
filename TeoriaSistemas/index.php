<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistema de Ventas - Login</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./styles/login.css" />
    <script defer src="./js/login.js"></script>
  </head>
  <body>
    <!-- Login Form -->
    <div class="login-container" id="loginContainer">
      <div class="login-header">
        <i class="fas fa-store"></i>
        <h1>Sistema de Ventas</h1>
        <p>Abarrotes La esquinita</p>
      </div>
      <form class="login-form" id="loginForm">
        <div id="alertContainer"></div>
        <div class="form-group">
          <label for="usuario">Usuario</label>
          <input type="text" id="usuario" name="usuario" required />
          <i class="fas fa-user"></i>
        </div>
        <div class="form-group">
          <label for="password">Contraseña</label>
          <input type="password" id="password" name="password" required />
          <i class="fas fa-lock"></i>
        </div>
        <button type="submit" class="btn-login">
          <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
        </button>
      </form>
    </div>

    <!-- Dashboard -->
    <div class="dashboard" id="dashboard">
      <aside class="sidebar">
        <div class="sidebar-header">
          <h2><i class="fas fa-store"></i> Abarrotes</h2>
          <p id="userInfo">Usuario: Admin</p>
        </div>
        <nav class="sidebar-menu">
          <a
            href="#dashboard"
            class="menu-item active"
            data-section="dashboard"
          >
            <i class="fas fa-tachometer-alt"></i> Panel de Control
          </a>
          <a href="/puntodeVenta.html" class="menu-item" data-section="ventas">
            <i class="fas fa-cash-register"></i> Punto de Venta
          </a>
          <a href="./gestionProd.php" class="menu-item">
            <i class="fas fa-box"></i> Productos
          </a>
          <!-- <a href="#clientes" class="menu-item" data-section="clientes">
            <i class="fas fa-users"></i> Clientes
          </a>
          <a href="#proveedores" class="menu-item" data-section="proveedores">
            <i class="fas fa-truck"></i> Proveedores
          </a>
          <a href="#compras" class="menu-item" data-section="compras">
            <i class="fas fa-shopping-cart"></i> Compras
          </a>
          <a href="#inventario" class="menu-item" data-section="inventario">
            <i class="fas fa-warehouse"></i> Inventario
          </a> -->
          <!-- <a href="#caja" class="menu-item" data-section="caja">
            <i class="fas fa-money-bill-wave"></i> Caja
          </a> -->
          <a href="./reportes.php" class="menu-item" data-section="reportes">
            <i class="fas fa-chart-bar"></i> Reportes
          </a>
          <!-- <a
            href="#configuracion"
            class="menu-item"
            data-section="configuracion"
          >
            <i class="fas fa-cog"></i> Configuración
          </a> -->
        </nav>
      </aside>

      <main class="main-content">
        <div class="top-bar">
          <h1 id="pageTitle">Dashboard</h1>
          <div>
            <a href="#" class="btn btn-primary">
              <i class="fas fa-plus"></i> Nueva Venta
            </a>
            <button onclick="logout()" class="btn btn-danger">
              <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </button>
          </div>
        </div>

        <div id="dashboardContent">
          <div class="stats-grid">
            <div class="stat-card sales">
              <div class="stat-icon">
                <i class="fas fa-shopping-cart"></i>
              </div>
              <div class="stat-value">125</div>
              <div class="stat-label">Ventas Hoy</div>
            </div>
            <div class="stat-card products">
              <div class="stat-icon">
                <i class="fas fa-box"></i>
              </div>
              <div class="stat-value">1,234</div>
              <div class="stat-label">Productos</div>
            </div>
            <div class="stat-card customers">
              <div class="stat-icon">
                <i class="fas fa-users"></i>
              </div>
              <div class="stat-value">89</div>
              <div class="stat-label">Clientes</div>
            </div>
            <div class="stat-card revenue">
              <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
              </div>
              <div class="stat-value">S/ 2,450</div>
              <div class="stat-label">Ingresos Hoy</div>
            </div>
          </div>

          <div class="stats-grid">
            <div class="stat-card">
              <h3>
                <i class="fas fa-exclamation-triangle"></i> Productos con Stock
                Bajo
              </h3>
              <ul style="margin-top: 15px">
                <li>Aceite Vegetal - Stock: 2</li>
                <li>Arroz Superior - Stock: 5</li>
                <li>Azúcar Blanca - Stock: 3</li>
              </ul>
            </div>
            <div class="stat-card">
              <h3><i class="fas fa-star"></i> Productos Más Vendidos</h3>
              <ul style="margin-top: 15px">
                <li>Coca Cola 500ml - 45 ventas</li>
                <li>Pan Integral - 38 ventas</li>
                <li>Leche Gloria - 32 ventas</li>
              </ul>
            </div>
          </div>
        </div>
      </main>
    </div>
  </body>
</html>
