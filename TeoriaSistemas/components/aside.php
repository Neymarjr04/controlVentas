<?php
?>
<aside id="sidebarMaster" class="sidebar  desactive">
    <div class="sidebar-header">
        <h2><i class="fas fa-store"></i> <p class="textoNavbar" > ABARROTES </p> </h2>          
        </p> 
          <i class="fa-solid fa-user"></i>
           <?php echo $_SESSION['nombre']; ?> 
          </p>
    </div>
    <nav class="sidebar-menu">
        <a
            href="#dashboard"
            class="menu-item"
            data-section="dashboard">
            <i class="fas fa-tachometer-alt"></i> 
            <p class="textoNavbar" > Panel de control </p>
        </a>
        <a href="/ventas" class="menu-item <?php echo ($direccion == "ventas")?"active":""?>" data-section="ventas">
            <i class="fas fa-cash-register"></i> 
            <p class="textoNavbar" > Punto de venta </p>

        </a>
        <a href="/productos" class="menu-item <?php echo ($direccion == "productos")?"active":""?> ">
            <i class="fas fa-box"></i> 
            <p class="textoNavbar" > Productos </p>

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
        <?php if($_SESSION["rol"] == "administrador" ){?>
        <a href="/caja" class="menu-item <?php echo ($direccion == "caja")?"active":""?>" data-section="caja">
          <i class="fa-solid fa-boxes-stacked"></i>
          <p class="textoNavbar" > Caja </p>
        </a> 
        <?php }?>
        <a href="/reportes" class="menu-item <?php echo ($direccion == "reporte")?"active":""?> " data-section="reportes">
            <i class="fas fa-chart-bar"></i> 
            <p class="textoNavbar" >Reportes</p>
        </a>
        <a
            href="cerrarSecion"
            class="menu-item"
            data-section="configuracion"
          >
          <i class="fa-solid fa-door-open"></i>
            <p class="textoNavbar" >Cerrar secion </p>
        </a>

        <div class="flechaNavbar menu-item ">
          <p>
            <i class="fa-solid fa-arrow-right"></i>
            <p class="textoNavbar" > Desplazador </p>
          </p>
        </div>

    </nav>
</aside>