<?php
  $direccion = "ventas";
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Punto de Venta - Sistema Abarrotes</title>
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="./styles/puntoVenta.css" />
    <link rel="stylesheet" href="./styles/navbar.css">
    <link rel="stylesheet" href="./styles/modal.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="./js/index.js"></script>
    <script defer src="./components/modal.js"></script>
    <script defer src="./js/puntoVenta.js"></script>
  </head>
  <body>
    <?php include "components/aside.php";?>
    <div class="pos-container container">
      <!-- Panel Izquierdo - Productos -->
      <div class="pos-left">
        <div class="pos-header">
          <h2><i class="fas fa-cash-register"></i> Punto de Venta</h2>
          <div>
            <span id="currentUser">Cajero: Admin</span>
            <button
              onclick="window.history.back()"
              class="btn btn-secondary"
              style="margin-left: 15px; padding: 8px 15px"
            >
              <i class="fas fa-arrow-left"></i> Volver
            </button>
          </div>
        </div>

        <div class="search-section">
          <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input
              type="text"
              class="search-input"
              id="searchProduct"
              placeholder="Buscar producto por nombre o código de barras..."
            />
          </div>
          <div class="quick-categories" id="categoriasData" >
            <button class="category-btn active" data-category="todos">
              Todos
            </button>
            <button class="category-btn" data-category="abarrotes">
              Abarrotes
            </button>
            <button class="category-btn" data-category="bebidas">
              Bebidas
            </button>
            <button class="category-btn" data-category="limpieza">
              Limpieza
            </button>
            <button class="category-btn" data-category="snacks">Snacks</button>
          </div>
        </div>

        <div class="products-grid">
          <div class="product-grid" id="productsGrid">
            <!-- Los productos se cargarán aquí dinámicamente -->
          </div>
        </div>
      </div>

      <!-- Panel Derecho - Carrito -->
      <div class="pos-right">
        <div class="cart-header">
          <h3><i class="fas fa-shopping-cart"></i> Carrito de Compras</h3>
          <p>Venta #<span id="saleNumber">V202408140001</span></p>
        </div>

        <div class="cart-items" id="cartItems">
          <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Carrito vacío</p>
            <p style="font-size: 14px">Selecciona productos para comenzar</p>
          </div>
        </div>

        <div class="cart-totals" id="cartTotals" style="display: none">
          <div class="total-row">
            <span>Subtotal:</span>
            <span id="subtotal">S/ 0.00</span>
          </div>
          <div class="total-row">
            <span>Descuento:</span>
            <span id="discount">S/ 0.00</span>
          </div>
          <div class="total-row">
            <span>IGV (18%):</span>
            <span id="tax">S/ 0.00</span>
          </div>
          <div class="total-row final">
            <span>TOTAL:</span>
            <span id="total">S/ 0.00</span>
          </div>
        </div>

        <div class="checkout-actions">
          <button
            class="btn btn-primary"
            onclick="openCheckoutModal()"
            id="checkoutBtn"
            disabled
          >
            <i class="fas fa-credit-card"></i> Procesar Venta
          </button>
          <button class="btn btn-danger" onclick="clearCart()">
            <i class="fas fa-trash"></i> Limpiar Carrito
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Checkout -->
    <div id="checkoutModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h3><i class="fas fa-credit-card"></i> Finalizar Venta</h3>
          <button class="close" onclick="closeCheckoutModal()">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Cliente (Opcional)</label>
            <select id="customerSelect">
              <option value="">Cliente Genérico</option>
              <option value="1">Juan Pérez - 12345678</option>
              <option value="2">María García - 87654321</option>
              <option value="3">Carlos López - 11223344</option>
            </select>
          </div>

          <div class="form-group">
            <label>Tipo de Comprobante</label>
            <select id="receiptType">
              <option value="ticket">Ticket</option>
              <option value="boleta">Boleta</option>
              <option value="factura">Factura</option>
            </select>
          </div>

          <div class="form-group">
            <label>Método de Pago</label>
            <div class="payment-methods">
              <div class="payment-method selected" data-method="efectivo">
                <i class="fas fa-money-bill-wave"></i>
                <p>Efectivo</p>
              </div>
              <div class="payment-method" data-method="tarjeta">
                <i class="fas fa-credit-card"></i>
                <p>Tarjeta</p>
              </div>
              <div class="payment-method" data-method="yape">
                <i class="fas fa-mobile-alt"></i>
                <p>Yape</p>
              </div>
              <div class="payment-method" data-method="plin">
                <i class="fas fa-phone"></i>
                <p>Plin</p>
              </div>
            </div>
          </div>

          <div class="form-group" id="cashAmountGroup">
            <label>Monto Recibido</label>
            <input
              type="number"
              id="cashAmount"
              placeholder="0.00"
              step="0.01"
            />
            <p
              id="changeAmount"
              style="margin-top: 10px; font-weight: bold; color: #28a745"
            ></p>
          </div>

          <div style="display: flex; gap: 10px; margin-top: 20px">
            <button
              class="btn btn-primary"
              onclick="processSale()"
              style="flex: 1"
            >
              <i class="fas fa-check"></i> Confirmar Venta
            </button>
            <button class="btn btn-secondary" onclick="closeCheckoutModal()">
              <i class="fas fa-times"></i> Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>

  </body>
</html>
