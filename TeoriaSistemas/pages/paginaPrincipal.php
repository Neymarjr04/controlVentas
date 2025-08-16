<div class="dashboard" id="dashboard">
    <?php require("componentes/aside.php"); ?>

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