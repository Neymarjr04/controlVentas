
let products = [
  {
    id: 1,
    nombre: "Coca Cola 500ml",
    precio_venta: 3.5,
    stock_actual: 45,
    categoria_nombre: "bebidas",
    codigo_barras: "7894900011517",
  },
];

let cart = [];
let currentCategory = "todos";
let categorias = [
  {
    id: 1,
    descripcion: "Productos básicos de alimentación",
    estado: 1,
    id: 1,
    nombre: "Abarrotes",
  },
];
// Inicializar la aplicación
const modal = new ModalControl({ tiempoCerrado: 0.5 });
let selecionCaja = 0;
document.addEventListener("DOMContentLoaded", function () {

  products = JSON.parse(localStorage.getItem("products"));
  categorias = JSON.parse(localStorage.getItem("categorias"));
  setCategorias();
  displayProducts();
  setupEventListeners();
  selectionCaja();
});

function selectionCaja(){
  const cajas = JSON.parse(sessionStorage.getItem("cajas")) || [];
  if(!cajas || cajas.length < 2 ){
    selecionCaja = cajas[0].id;
    return
  }

  let contenido = "";
  let empaquetador = document.createElement("div");
  empaquetador.classList.add("contenedor-cajas");
  cajas.forEach((caja)=>{
    contenido += `
    <div class="caja" onclick="selectCaja(${caja.id})" >
      <p> ${caja.nombre} </p>
      <p> ${caja.fecha_apertura} </p>
    </div> 
    `;
  })
  empaquetador.innerHTML = contenido;

  modal.setContenidoModal("Seleccione la caja",empaquetador);
  modal.activarModal();

}

function selectCaja(cajaData){
  selecionCaja = cajaData;
  modal.cerrarEvento();
}

function setCategorias() {
  const categori = document.getElementById("categoriasData");
  categori.innerHTML = `
  <button class="category-btn active" data-category="todos">
    Todos
  </button>
  `;
  categori.innerHTML += categorias.map(
    (categoria) => `
    <button class="category-btn" data-category="${categoria.id}">
      ${categoria.nombre}
    </button>
  `);
}

function setupEventListeners() {
  // Búsqueda de productos
  document
    .getElementById("searchProduct")
    .addEventListener("input", function (e) {
      const searchTerm = e.target.value.toLowerCase();
      displayProducts(searchTerm);
    });

  // Categorías
  document.querySelectorAll(".category-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      document
        .querySelectorAll(".category-btn")
        .forEach((b) => b.classList.remove("active"));
      this.classList.add("active");
      currentCategory = this.getAttribute("data-category");
      displayProducts();
    });
  });

  // Métodos de pago
  document.querySelectorAll(".payment-method").forEach((method) => {
    method.addEventListener("click", function () {
      document
        .querySelectorAll(".payment-method")
        .forEach((m) => m.classList.remove("selected"));
      this.classList.add("selected");

      const cashGroup = document.getElementById("cashAmountGroup");
      if (this.getAttribute("data-method") === "efectivo") {
        cashGroup.style.display = "block";
      } else {
        cashGroup.style.display = "none";
      }
    });
  });

  // Cálculo de cambio
  document.getElementById("cashAmount").addEventListener("input", function () {
    calculateChange();
  });

  // Teclas rápidas
  document.addEventListener("keydown", function (e) {
    if (e.key === "F1") {
      e.preventDefault();
      document.getElementById("searchProduct").focus();
    }
    if (e.key === "F2") {
      e.preventDefault();
      if (cart.length > 0) {
        openCheckoutModal();
      }
    }
    if (e.key === "Escape") {
      closeCheckoutModal();
    }
  });
}

function displayProducts(searchTerm = "") {
  const grid = document.getElementById("productsGrid");
  let filteredProducts = products;

  // Filtrar por categoría
  if (currentCategory !== "todos") {
    filteredProducts = products.filter(
      (product) => product.categoria_id == currentCategory
    );
  }

  // Filtrar por búsqueda
  if (searchTerm) {
    filteredProducts = filteredProducts.filter(
      (product) =>
        product.nombre.toLowerCase().includes(searchTerm) ||
        product.codigo_barras.includes(searchTerm)
    );
  }

  grid.innerHTML = filteredProducts
    .map(
      (product) => `
                <div class="product-card" onclick="addToCart(${product.id})">
                    <div class="product-image">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="product-name">${product.nombre}</div>
                    <div class="product-price">S/ ${product.precio_venta.toFixed(
                      2
                    )}</div>
                    <div class="product-stock">Stock: ${
                      product.stock_actual
                    }</div>
                </div>
            `
    )
    .join("");
}

function addToCart(productId) {
  const product = products.find((p) => p.id === productId);
  console.log(product);
  if (!product) return;

  const existingItem = cart.find((item) => item.id === productId);

  if (existingItem) {
    if (existingItem.quantity < product.stock_actual) {
      existingItem.quantity++;
      existingItem.subtotal = existingItem.quantity * existingItem.precio_venta;
    }
  } else {
    cart.push({
      id: product.id,
      nombre: product.nombre,
      precio_venta: product.precio_venta,
      quantity: 1,
      subtotal: product.precio_venta,
      stock_actual: product.stock_actual,
    });
  }

  updateCartDisplay();
}

function removeFromCart(productId) {
  cart = cart.filter((item) => item.id !== productId);
  updateCartDisplay();
}

function updateQuantity(productId, newQuantity) {
  const item = cart.find((item) => item.id === productId);
  if (!item) return;

  if (newQuantity <= 0) {
    removeFromCart(productId);
    return;
  }

  if (newQuantity > item.stock_actual) {
    alert(`Stock máximo disponible: ${item.stock_actual}`);
    return;
  }

  item.quantity = newQuantity;
  item.subtotal = item.quantity * item.precio_venta;
  updateCartDisplay();
}

function updateCartDisplay() {
  const cartItems = document.getElementById("cartItems");
  const cartTotals = document.getElementById("cartTotals");
  const checkoutBtn = document.getElementById("checkoutBtn");

  if (cart.length === 0) {
    cartItems.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Carrito vacío</p>
                        <p style="font-size: 14px;">Selecciona productos para comenzar</p>
                    </div>`;
    cartTotals.style.display = "none";
    checkoutBtn.disabled = true;
    return;
  }

  cartItems.innerHTML = cart
    .map(
      (item) => `
                <div class="cart-item">
                    <div class="item-info">
                        <div class="item-name">${item.nombre}</div>
                        <div class="item-price">S/ ${item.precio_venta.toFixed(
                          2
                        )} c/u</div>
                        <div class="quantity-controls">
                            <button class="qty-btn" onclick="updateQuantity(${
                              item.id
                            }, ${item.quantity - 1})">-</button>
                            <input type="number" class="qty-input" value="${
                              item.quantity
                            }" 
                                   onchange="updateQuantity(${
                                     item.id
                                   }, parseInt(this.value))"
                                   min="1" max="${item.stock_actual}">
                            <button class="qty-btn" onclick="updateQuantity(${
                              item.id
                            }, ${item.quantity + 1})">+</button>
                        </div>
                    </div>
                    <div class="item-total">S/ ${item.subtotal.toFixed(2)}</div>
                    <div class="remove-item" onclick="removeFromCart(${
                      item.id
                    })">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            `
    )
    .join("");

  // Calcular totales
  const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
  const discount = 0; // Por ahora sin descuentos
  const tax = subtotal * 0.18;
  const total = subtotal + tax - discount;

  document.getElementById("subtotal").textContent = `S/ ${subtotal.toFixed(2)}`;
  document.getElementById("discount").textContent = `S/ ${discount.toFixed(2)}`;
  document.getElementById("tax").textContent = `S/ ${tax.toFixed(2)}`;
  document.getElementById("total").textContent = `S/ ${total.toFixed(2)}`;

  cartTotals.style.display = "block";
  checkoutBtn.disabled = false;
}

function openCheckoutModal() {
  document.getElementById("checkoutModal").style.display = "block";
}

function closeCheckoutModal() {
  document.getElementById("checkoutModal").style.display = "none";
}

function calculateChange() {
  const total = parseFloat(
    document.getElementById("total").textContent.replace("S/ ", "")
  );
  const cashAmount =
    parseFloat(document.getElementById("cashAmount").value) || 0;
  const change = cashAmount - total;

  const changeElement = document.getElementById("changeAmount");
  if (change >= 0) {
    changeElement.textContent = `Cambio: S/ ${change.toFixed(2)}`;
    changeElement.style.color = "#28a745";
  } else {
    changeElement.textContent = `Falta: S/ ${Math.abs(change).toFixed(2)}`;
    changeElement.style.color = "#dc3545";
  }
}

function processSale() {
  // Validaciones
  const selectedMethod = document
    .querySelector(".payment-method.selected")
    .getAttribute("data-method");
  if (selectedMethod === "efectivo") {
    const total = parseFloat(
      document.getElementById("total").textContent.replace("S/ ", "")
    );
    const cashAmount =
      parseFloat(document.getElementById("cashAmount").value) || 0;
    if (cashAmount < total) {
      showAlertConfirm("El monto recibido es menor al total de la venta","error");
      return;
    }
  }
  const datos = {
    caja:selecionCaja,
    datos:cart
  }
  console.log(datos);
  $.post("./model/tasks/generateVentaTask.php",datos,(response)=>{
    const respuesta = JSON.parse(response);
    if(respuesta.status == "bien"){
      console.log(respuesta);
    }
    alert("Venta procesada exitosamente!");
    clearCart();
    closeCheckoutModal();
  });
  // Simular procesamiento

  // Limpiar carrito y cerrar modal
}

function clearCart() {
  cart = [];
  updateCartDisplay();
}
function showAlertConfirm(message,type){
  Swal.fire({
    title: message,
    icon: type,
    draggable: true
  });
}
function holdSale() {
  if (cart.length === 0) {
    showAlertConfirm("No hay productos en el carrito","error");
    return;
  }
  showAlertConfirm(
    "Venta retenida. Podrás recuperarla desde el menú de ventas pendientes.","error"
  );
}

// Mostrar teclas de acceso rápido
console.log("Teclas rápidas:");
console.log("F1 - Buscar producto");
console.log("F2 - Procesar venta");
console.log("ESC - Cerrar modal");
