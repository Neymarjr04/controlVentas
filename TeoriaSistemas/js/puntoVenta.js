const products = [
  {
    id: 1,
    name: "Coca Cola 500ml",
    price: 3.5,
    stock: 45,
    category: "bebidas",
    barcode: "7894900011517",
  },
  {
    id: 2,
    name: "Pan Integral",
    price: 4.2,
    stock: 25,
    category: "abarrotes",
    barcode: "7894900011518",
  },
  {
    id: 3,
    name: "Leche Gloria UHT",
    price: 4.8,
    stock: 32,
    category: "abarrotes",
    barcode: "7894900011519",
  },
  {
    id: 4,
    name: "Aceite Primor 1L",
    price: 8.5,
    stock: 15,
    category: "abarrotes",
    barcode: "7894900011520",
  },
  {
    id: 5,
    name: "Detergente Ariel",
    price: 12.9,
    stock: 8,
    category: "limpieza",
    barcode: "7894900011521",
  },
  {
    id: 6,
    name: "Galletas Oreo",
    price: 5.2,
    stock: 28,
    category: "snacks",
    barcode: "7894900011522",
  },
  {
    id: 7,
    name: "Arroz Costeño 5kg",
    price: 18.5,
    stock: 12,
    category: "abarrotes",
    barcode: "7894900011523",
  },
  {
    id: 8,
    name: "Inca Kola 500ml",
    price: 3.2,
    stock: 38,
    category: "bebidas",
    barcode: "7894900011524",
  },
  {
    id: 9,
    name: "Papel Higiénico Suave",
    price: 6.8,
    stock: 22,
    category: "limpieza",
    barcode: "7894900011525",
  },
  {
    id: 10,
    name: "Yogurt Gloria Fresa",
    price: 3.8,
    stock: 18,
    category: "abarrotes",
    barcode: "7894900011526",
  },
];

let cart = [];
let currentCategory = "todos";

// Inicializar la aplicación
document.addEventListener("DOMContentLoaded", function () {
  displayProducts();
  setupEventListeners();
});

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
      (product) => product.category === currentCategory
    );
  }

  // Filtrar por búsqueda
  if (searchTerm) {
    filteredProducts = filteredProducts.filter(
      (product) =>
        product.name.toLowerCase().includes(searchTerm) ||
        product.barcode.includes(searchTerm)
    );
  }

  grid.innerHTML = filteredProducts
    .map(
      (product) => `
                <div class="product-card" onclick="addToCart(${product.id})">
                    <div class="product-image">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="product-name">${product.name}</div>
                    <div class="product-price">S/ ${product.price.toFixed(
                      2
                    )}</div>
                    <div class="product-stock">Stock: ${product.stock}</div>
                </div>
            `
    )
    .join("");
}

function addToCart(productId) {
  const product = products.find((p) => p.id === productId);
  if (!product) return;

  const existingItem = cart.find((item) => item.id === productId);

  if (existingItem) {
    if (existingItem.quantity < product.stock) {
      existingItem.quantity++;
      existingItem.subtotal = existingItem.quantity * existingItem.price;
    }
  } else {
    cart.push({
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: 1,
      subtotal: product.price,
      stock: product.stock,
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

  if (newQuantity > item.stock) {
    alert(`Stock máximo disponible: ${item.stock}`);
    return;
  }

  item.quantity = newQuantity;
  item.subtotal = item.quantity * item.price;
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
                        <div class="item-name">${item.name}</div>
                        <div class="item-price">S/ ${item.price.toFixed(
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
                                   min="1" max="${item.stock}">
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
      alert("El monto recibido es menor al total de la venta");
      return;
    }
  }

  // Simular procesamiento
  alert("Venta procesada exitosamente!");

  // Limpiar carrito y cerrar modal
  clearCart();
  closeCheckoutModal();
}

function clearCart() {
  cart = [];
  updateCartDisplay();
}

function holdSale() {
  if (cart.length === 0) {
    alert("No hay productos en el carrito");
    return;
  }
  alert(
    "Venta retenida. Podrás recuperarla desde el menú de ventas pendientes."
  );
}

// Mostrar teclas de acceso rápido
console.log("Teclas rápidas:");
console.log("F1 - Buscar producto");
console.log("F2 - Procesar venta");
console.log("ESC - Cerrar modal");
