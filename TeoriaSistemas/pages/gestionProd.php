<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Productos - Sistema Abarrotes</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./styles/gestionProd.css" />
    <link rel="stylesheet" href="./styles/navbar.css">
    <script defer src="./js/gestionProd.js"></script>
  </head>
  <body>
    <?php include "components/aside.php"; ?>
    <div class="container">
      <div class="header">
        <h1>
          <i class="fas fa-box"></i>
          Gestión de Productos
        </h1>
        <div class="header-actions">
          <button class="btn btn-primary" onclick="openProductModal()">
            <i class="fas fa-plus"></i> Nuevo Producto
          </button>
          <button class="btn btn-info" onclick="showStockAlert()">
            <i class="fas fa-exclamation-triangle"></i> Stock Bajo
          </button>
          <button class="btn btn-secondary" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Volver
          </button>
        </div>
      </div>

      <div id="alertContainer"></div>

      <div class="filters-card">
        <div class="filters-row">
          <div class="form-group">
            <label>Buscar producto</label>
            <input
              type="text"
              id="searchInput"
              placeholder="Buscar por nombre, código o categoría..."
            />
          </div>
          <div class="form-group">
            <label>Categoría</label>
            <select id="categoryFilter">
              <option value="">Todas las categorías</option>
              <option value="abarrotes">Abarrotes</option>
              <option value="bebidas">Bebidas</option>
              <option value="limpieza">Limpieza</option>
              <option value="snacks">Snacks</option>
              <option value="lacteos">Lácteos</option>
            </select>
          </div>
          <div class="form-group">
            <label>Estado</label>
            <select id="statusFilter">
              <option value="">Todos</option>
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" onclick="loadProducts()">
              <i class="fas fa-search"></i> Filtrar
            </button>
          </div>
        </div>
      </div>

      <div class="products-table">
        <div class="table-header">
          <div class="table-title">Lista de Productos</div>
          <div class="table-stats" id="tableStats">
            Total: <span id="totalProducts">0</span> productos
          </div>
        </div>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Producto</th>
                <th>Código</th>
                <th>Categoría</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="productsTableBody">
              <!-- Los productos se cargarán aquí dinámicamente -->
            </tbody>
          </table>
        </div>
        <div class="pagination" id="pagination">
          <!-- Paginación se generará aquí -->
        </div>
      </div>
    </div>

    <!-- Modal de Producto -->
    <div id="productModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3 id="modalTitle"><i class="fas fa-box"></i> Nuevo Producto</h3>
          <button class="close" onclick="closeProductModal()">&times;</button>
        </div>
        <div class="modal-body">
          <form id="productForm">
            <input type="hidden" id="productId" />
            <div class="form-grid">
              <div class="form-group">
                <label for="productName">Nombre del Producto *</label>
                <input type="text" id="productName" required />
              </div>
              <div class="form-group">
                <label for="productBarcode">Código de Barras</label>
                <input type="text" id="productBarcode" />
              </div>
              <div class="form-group">
                <label for="productCategory">Categoría *</label>
                <select id="productCategory" required>
                  <option value="">Seleccionar categoría</option>
                  <option value="1">Abarrotes</option>
                  <option value="2">Bebidas</option>
                  <option value="3">Limpieza</option>
                  <option value="4">Snacks</option>
                  <option value="5">Lácteos</option>
                </select>
              </div>
              <div class="form-group">
                <label for="productUnit">Unidad de Medida</label>
                <select id="productUnit">
                  <option value="unidad">Unidad</option>
                  <option value="kg">Kilogramo</option>
                  <option value="g">Gramo</option>
                  <option value="l">Litro</option>
                  <option value="ml">Mililitro</option>
                  <option value="paquete">Paquete</option>
                  <option value="caja">Caja</option>
                </select>
              </div>
              <div class="form-group">
                <label for="purchasePrice">Precio de Compra *</label>
                <input
                  type="number"
                  id="purchasePrice"
                  step="0.01"
                  min="0"
                  required
                />
              </div>
              <div class="form-group">
                <label for="salePrice">Precio de Venta *</label>
                <input
                  type="number"
                  id="salePrice"
                  step="0.01"
                  min="0"
                  required
                />
              </div>
              <div class="form-group">
                <label for="currentStock">Stock Actual</label>
                <input type="number" id="currentStock" min="0" value="0" />
              </div>
              <div class="form-group">
                <label for="minStock">Stock Mínimo</label>
                <input type="number" id="minStock" min="0" value="5" />
              </div>
              <div class="form-group form-grid-full">
                <label for="productDescription">Descripción</label>
                <textarea
                  id="productDescription"
                  rows="3"
                  style="resize: vertical"
                ></textarea>
              </div>
            </div>
            <div style="margin-top: 25px; display: flex; gap: 10px">
              <button type="submit" class="btn btn-primary" style="flex: 1">
                <i class="fas fa-save"></i> Guardar Producto
              </button>
              <button
                type="button"
                class="btn btn-secondary"
                onclick="closeProductModal()"
              >
                <i class="fas fa-times"></i> Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
