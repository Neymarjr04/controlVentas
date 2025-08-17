<?php
session_start();

$direccion = "caja";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> CAJA | control </title>
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="./styles/gestionProd.css" />
  <link rel="stylesheet" href="./styles/navbar.css">
  <link rel="stylesheet" href="./styles/caja.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer src="./js/index.js"></script>
  <script defer src="./js/caja.js"></script>
</head>

<body>
  <?php include "components/aside.php"; ?>
  <?php
  if ($_SESSION['rol'] != "administrador") {
  ?>
    <p>No tienes permisos para acceder aqui</p>
  <?php
    return;
  }
  ?>

  <div class="container">
    <div class="header">
      <h1>
        <i class="fas fa-cash-register"></i>
        Gestión de Cajas
      </h1>
      <div class="header-actions">
        <button class="btn btn-primary" onclick="openCajaModal()">
          <i class="fas fa-plus"></i> Nueva Caja
        </button>
        <button class="btn btn-secondary" onclick="window.history.back()">
          <i class="fas fa-arrow-left"></i> Volver
        </button>
      </div>
    </div>

    <div class="contenedor-caja">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Monto Inicial</th>
            <th>Monto Actual</th>
            <th>Fecha Apertura</th>
            <th>Fecha Cierre</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="tablaCajas">
        </tbody>
      </table>
    </div>
  </div>

  </div>

  <div id="productModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 id="modalTitle"><i class="fas fa-box"></i> Nueva Caja</h3>
        <button class="close" onclick="closeProductModal()">&times;</button>
      </div>
      <div class="modal-body">
        <form id="productForm">
          <input type="hidden" id="productId" />
          <div class="form-grid">
            <div class="form-group">
              <label for="productName">Nombre de la caja</label>
              <input type="text" id="nombreCaja" placeholder="ej. Caja 1" required />
            </div>

            <div class="form-group">
              <label for="salePrice">Monto Inicial</label>
              <input
                type="number"
                id="montoInicialCaja"
                step="0.01"
                min="0"
                required
                value="0" />
            </div>

            <div class="form-group form-grid-full">
              <label for="productDescription">Observaciones</label>
              <textarea
                id="cajaDescription"
                rows="3"
                style="resize: vertical"></textarea>
            </div>
          </div>
          <div style="margin-top: 25px; display: flex; gap: 10px">
            <button type="submit" class="btn btn-primary" style="flex: 1">
              <i class="fas fa-save"></i> Guardar Caja
            </button>
            <button
              type="button"
              class="btn btn-secondary"
              onclick="closeProductModal()">
              <i class="fas fa-times"></i> Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>

</html>