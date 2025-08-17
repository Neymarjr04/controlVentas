let products = [
  {
    id: 1,
    nombre: "Coca Cola 500ml",
    codigo_barras: "7894900011517",
    categoria_id: 2,
    categoria_nombre: "Bebidas",
    descripcion: "Bebida gaseosa sabor cola",
    precio_compra: 2.8,
    precio_venta: 3.5,
    stock_actual: 45,
    stock_minimo: 10,
    unidad_medida: "unidad",
    estado: 1,
    fecha_registro: "2024-08-01",
  },
];
products = JSON.parse(localStorage.getItem("products"));
let currentPage = 1;
let itemsPerPage = 10;
let filteredProducts = [...products];
let editingProduct = null;
//localStorage.setItem("products", JSON.stringify(products));
// Inicializar la aplicación
document.addEventListener("DOMContentLoaded", function () {
  loadCategorias();
  setupEventListeners();
  loadProducts();
});

function loadCategorias() {
  const htmlcategoria = document.getElementById("categoryFilter");
  const productosCategory = document.getElementById("productCategory");
  const categorias = JSON.parse(localStorage.getItem("categorias"));
  let textCategoria = `<option value="">Todas las categorías</option>`;

  categorias.forEach((categoria) => {
    if (categoria.estado == "1") {
      textCategoria += `<option value="${categoria.id}"> ${categoria.nombre} </option>`;
    }
  });
  htmlcategoria.innerHTML = textCategoria;
  productosCategory.innerHTML = textCategoria;
}

function setupEventListeners() {
  // Búsqueda en tiempo real
  document.getElementById("searchInput").addEventListener("input", function () {
    currentPage = 1;
    loadProducts();
  });

  // Filtros
  document
    .getElementById("categoryFilter")
    .addEventListener("change", function () {
      currentPage = 1;
      loadProducts();
    });

  document
    .getElementById("statusFilter")
    .addEventListener("change", function () {
      currentPage = 1;
      loadProducts();
    });

  // Formulario de producto
  document
    .getElementById("productForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      saveProduct();
    });

  // Auto-calcular precio de venta basado en margen
  document
    .getElementById("purchasePrice")
    .addEventListener("input", function () {
      const purchasePrice = parseFloat(this.value) || 0;
      const margin = 1.3; // 30% de margen por defecto
      const salePrice = (purchasePrice * margin).toFixed(2);
      document.getElementById("salePrice").value = salePrice;
    });
}

function loadProducts() {
  const searchTerm = document.getElementById("searchInput").value.toLowerCase();
  const categoryFilter = document.getElementById("categoryFilter").value;
  const statusFilter = document.getElementById("statusFilter").value;

  console.log(categoryFilter);

  filteredProducts = products.filter((product) => {
    const matchesSearch =
      !searchTerm ||
      product.nombre.toLowerCase().includes(searchTerm) ||
      product.codigo_barras.toLowerCase().includes(searchTerm) ||
      product.categoria_nombre.toLowerCase().includes(searchTerm);

    const matchesCategory =
      !categoryFilter || product.categoria_id == categoryFilter;

    const matchesStatus = statusFilter === "" || product.estado == statusFilter;

    return matchesSearch && matchesCategory && matchesStatus;
  });

  displayProducts();
  updatePagination();
  updateStats();
}

function productosLoad(product) {
  let datos = `
                  <tr>
                      <td>
                          <div class="product-info">
                              <div class="product-image">
                                  <i class="fas fa-box"></i>
                              </div>
                              <div class="product-details">
                                  <h4>${product.nombre}</h4>
                                  <p>${
                                    product.descripcion || "Sin descripción"
                                  }</p>
                              </div>
                          </div>
                      </td>
                      <td>${product.codigo_barras || "N/A"}</td>
                      <td>${product.categoria_nombre}</td>
                      <td class="price-cell">S/ ${product.precio_compra.toFixed(
                        2
                      )}</td>
                      <td class="price-cell">S/ ${product.precio_venta.toFixed(
                        2
                      )}</td>
                      <td>
                          <span class="stock-badge ${getStockClass(
                            product.stock_actual,
                            product.stock_minimo
                          )}">
                              ${product.stock_actual} ${product.unidad_medida}
                          </span>
                      </td>
                      <td>
                          <span class="status-badge ${
                            product.estado ? "status-active" : "status-inactive"
                          }">
                              ${product.estado ? "Activo" : "Inactivo"}
                          </span>
                      </td>
                      <td>
                          <div class="action-buttons">
                              <button class="btn btn-info btn-sm" onclick="editProduct(${
                                product.id
                              })" title="Editar">
                                  <i class="fas fa-edit"></i>
                              </button>
                              <button class="btn btn-warning btn-sm" onclick="adjustStock(${
                                product.id
                              })" title="Ajustar Stock">
                                  <i class="fas fa-warehouse"></i>
                              </button>
                              <button class="btn btn-danger btn-sm" onclick="toggleProductStatus(${
                                product.id
                              })" title="Cambiar Estado">
                                  <i class="fas fa-power-off"></i>
                              </button>
                          </div>
                      </td>
                  </tr>
              `;
  return datos;
}

function displayProducts() {
  const tbody = document.getElementById("productsTableBody");
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const pageProducts = filteredProducts.slice(startIndex, endIndex);

  if (pageProducts.length === 0) {
    tbody.innerHTML = `
                      <tr>
                          <td colspan="8" style="text-align: center; padding: 40px; color: #6c757d;">
                              <i class="fas fa-box" style="font-size: 3em; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                              No se encontraron productos
                          </td>
                      </tr>`;
    return;
  }

  tbody.innerHTML = pageProducts
    .map((product) => productosLoad(product))
    .join("");
}

function getStockClass(current, minimum) {
  if (current <= minimum) return "stock-low";
  if (current <= minimum * 2) return "stock-medium";
  return "stock-high";
}

function updatePagination() {
  const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
  const pagination = document.getElementById("pagination");

  if (totalPages <= 1) {
    pagination.innerHTML = "";
    return;
  }

  let paginationHTML = `
                  <button onclick="changePage(${currentPage - 1})" ${
    currentPage <= 1 ? "disabled" : ""
  }>
                      <i class="fas fa-chevron-left"></i>
                  </button>
              `;

  // Mostrar páginas
  const maxVisiblePages = 5;
  let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
  let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

  if (endPage - startPage + 1 < maxVisiblePages) {
    startPage = Math.max(1, endPage - maxVisiblePages + 1);
  }

  for (let i = startPage; i <= endPage; i++) {
    paginationHTML += `
                      <button onclick="changePage(${i})" ${
      i === currentPage ? 'class="current-page"' : ""
    }>
                          ${i}
                      </button>
                  `;
  }

  paginationHTML += `
                  <button onclick="changePage(${currentPage + 1})" ${
    currentPage >= totalPages ? "disabled" : ""
  }>
                      <i class="fas fa-chevron-right"></i>
                  </button>
              `;

  pagination.innerHTML = paginationHTML;
}

function updateStats() {
  document.getElementById("totalProducts").textContent =
    filteredProducts.length;
}

function changePage(page) {
  const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
  if (page < 1 || page > totalPages) return;

  currentPage = page;
  displayProducts();
  updatePagination();
}

function openProductModal(productId = null) {
  editingProduct = productId;
  const modal = document.getElementById("productModal");
  const title = document.getElementById("modalTitle");

  if (productId) {
    const product = products.find((p) => p.id === productId);
    title.innerHTML = '<i class="fas fa-edit"></i> Editar Producto';

    // Llenar formulario
    document.getElementById("productId").value = product.id;
    document.getElementById("productName").value = product.nombre;
    document.getElementById("productBarcode").value =
      product.codigo_barras || "";
    document.getElementById("productCategory").value = product.categoria_id;
    document.getElementById("productUnit").value = product.unidad_medida;
    document.getElementById("purchasePrice").value = product.precio_compra;
    document.getElementById("salePrice").value = product.precio_venta;
    document.getElementById("currentStock").value = product.stock_actual;
    document.getElementById("minStock").value = product.stock_minimo;
    document.getElementById("productDescription").value =
      product.descripcion || "";
  } else {
    title.innerHTML = '<i class="fas fa-plus"></i> Nuevo Producto';
    document.getElementById("productForm").reset();
    document.getElementById("productId").value = "";
    document.getElementById("productUnit").value = "unidad";
    document.getElementById("currentStock").value = "0";
    document.getElementById("minStock").value = "5";
  }

  modal.style.display = "block";
}

function closeProductModal() {
  document.getElementById("productModal").style.display = "none";
  editingProduct = null;
}

function saveProduct() {
  const form = document.getElementById("productForm");
  const formData = new FormData(form);

  const productData = {
    id: document.getElementById("productId").value || null,
    nombre: document.getElementById("productName").value,
    codigo_barras: document.getElementById("productBarcode").value,
    categoria_id: parseInt(document.getElementById("productCategory").value),
    unidad_medida: document.getElementById("productUnit").value,
    precio_compra: parseFloat(document.getElementById("purchasePrice").value),
    precio_venta: parseFloat(document.getElementById("salePrice").value),
    stock_actual: parseInt(document.getElementById("currentStock").value),
    stock_minimo: parseInt(document.getElementById("minStock").value),
    descripcion: document.getElementById("productDescription").value,
    estado: 1,
  };

  // Validaciones
  if (
    !productData.nombre ||
    !productData.precio_venta ||
    !productData.categoria_id
  ) {
    showAlert("Por favor complete todos los campos requeridos", "error");
    return;
  }

  if (productData.precio_venta <= productData.precio_compra) {
    if (
      !confirm(
        "El precio de venta es menor o igual al precio de compra. ¿Desea continuar?"
      )
    ) {
      return;
    }
  }

  // Obtener nombre de categoría
  const categories = {
    1: "Abarrotes",
    2: "Bebidas",
    3: "Limpieza",
    4: "Snacks",
    5: "Lácteos",
  };
  productData.categoria_nombre = categories[productData.categoria_id];
  productData.fecha_registro = new Date().toISOString().split("T")[0];

  if (editingProduct) {
    // Actualizar producto existente
    const index = products.findIndex((p) => p.id === editingProduct);
    if (index !== -1) {
      products[index] = { ...products[index], ...productData };
      showAlert("Producto actualizado exitosamente", "success");
    }
  } else {
    // Crear nuevo producto
    productData.id = Math.max(...products.map((p) => p.id)) + 1;
    products.push(productData);
    showAlert("Producto creado exitosamente", "success");
  }

  closeProductModal();
  loadProducts();
}

function editProduct(id) {
  openProductModal(id);
}

function adjustStock(id) {
  const product = products.find((p) => p.id === id);
  if (!product) return;

  const texto = `Ajustar stock para: ${product.nombre}\n` +
      `Stock actual: ${product.stock_actual}\n` +
      `Ingrese nuevo stock:`;

  showAlertInput(texto,product.stock_actual,(valor) => {
    if (valor !== null) {
      const stock = parseInt(valor);
      if (isNaN(stock) || stock < 0) {
        showAlertConfirm("Stock debe ser un número válido mayor o igual a 0");
        return;
      }
      
      product.stock_actual = stock;
      showAlertConfirm(`Stock actualizado para ${product.nombre}`, "success");
      loadProducts();
    }
  });
}

function toggleProductStatus(id) {
  const product = products.find((p) => p.id === id);
  console.log(id);

  if (!product) return;

  const action = product.estado ? "desactivar" : "activar";
  const action1 = product.estado ? false : true;

  let texto = `¿Está seguro de ${action} el producto "${product.nombre}"?`;
  const data = {
    idProducto: id,
    tipo: action1,
  };
  console.log(data);
  showAlert(texto, "warning", () => {
    product.estado = action1 ? 1:0;
    $.post("./model/tasks/updateEstadoProductoTask.php", data, (response) => {
      console.log(response);
      localStorage.setItem("products",JSON.stringify(products));
      loadProducts();
    });
  });
}

function showStockAlert() {
  const lowStockProducts = products.filter(
    (p) => p.stock_actual <= p.stock_minimo && p.estado === 1
  );

  if (lowStockProducts.length === 0) {
    Swal.fire({
      title: "Excelente",
      text: "¡Excelente! No hay productos con stock bajo.",
      icon: "success",
    });
    return;
  }

  let message = `PRODUCTOS CON STOCK BAJO (${lowStockProducts.length}):\n\n`;
  lowStockProducts.forEach((product) => {
    message += `• ${product.nombre}: ${product.stock_actual} (mín: ${product.stock_minimo})\n`;
  });
  Swal.fire({
    title: "Oh No",
    text: message,
    icon: "question",
  });
}

function showAlertConfirm(message,type){
  Swal.fire({
    title: message,
    icon: type,
    draggable: true
  });
}

function showAlert(message, type, funcion = () => {}) {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
    },
    buttonsStyling: false,
  });
  swalWithBootstrapButtons
    .fire({
      title: "Estas seguro ?",
      text: message,
      icon: type,
      showCancelButton: true,
      confirmButtonText: "Si desactivar",
      cancelButtonText: "No, cancelar",
      reverseButtons: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        funcion();
        swalWithBootstrapButtons.fire({
          title: "Producto Desactivado!",
          text: "Su producto ha sido desactivado",
          icon: "success",
        });
      } else if (result.dismiss === Swal.DismissReason.cancel) {
        swalWithBootstrapButtons.fire({
          title: "Cancelado",
          text: "No se elimino",
          icon: "error",
        });
      }
    });
}

/**
 * 
 * @param {string} titulo 
 * @param {string} valorDefault 
 * @param {void} callback 
 */
function showAlertInput(titulo,valorDefault = "text",callback) {
  Swal.fire({
    title: titulo,
    input: "text",
    inputValue:valorDefault,
    inputAttributes: {
      autocapitalize: "off",
    },
    showCancelButton: true,
    confirmButtonText: "Update",
    showLoaderOnConfirm: true,
    
  }).then((result) => {
    if(result.isConfirmed){
      callback(result.value);
    }else{
      callback(null);
    }
  });
}

// Cerrar modal al hacer clic fuera
window.onclick = function (event) {
  const modal = document.getElementById("productModal");
  if (event.target === modal) {
    closeProductModal();
  }
};

// Teclas de acceso rápido
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    closeProductModal();
  }
  if (e.ctrlKey && e.key === "n") {
    e.preventDefault();
    openProductModal();
  }
});

console.log("Teclas de acceso rápido:");
console.log("Ctrl+N - Nuevo producto");
console.log("ESC - Cerrar modal");
