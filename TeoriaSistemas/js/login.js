const demoUsers = {
  admin: {
    password: "admin123",
    nombre: "Administrador",
    rol: "administrador",
  },
  cajero: {
    password: "cajero123",
    nombre: "Cajero Principal",
    rol: "cajero",
  },
  vendedor: {
    password: "vendedor123",
    nombre: "Vendedor",
    rol: "vendedor",
  },
};

// Login functionality
document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const usuario = document.getElementById("usuario").value;
  const password = document.getElementById("password").value;

  if (demoUsers[usuario] && demoUsers[usuario].password === password) {
    // Simular login exitoso
    sessionStorage.setItem("currentUser", JSON.stringify(demoUsers[usuario]));
    showAlert("Login exitoso", "success");

    setTimeout(() => {
      document.getElementById("loginContainer").style.display = "none";
      document.getElementById("dashboard").style.display = "block";
      document.getElementById(
        "userInfo"
      ).textContent = `Usuario: ${demoUsers[usuario].nombre}`;
    }, 1000);
  } else {
    showAlert("Usuario o contraseña incorrectos", "error");
  }
});

function showAlert(message, type) {
  const alertContainer = document.getElementById("alertContainer");
  alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;

  setTimeout(() => {
    alertContainer.innerHTML = "";
  }, 3000);
}

// Menu navigation
document.querySelectorAll(".menu-item").forEach((item) => {
  item.addEventListener("click", function (e) {
    //e.preventDefault();

    // Remove active class from all items
    document.querySelectorAll(".menu-item").forEach((menuItem) => {
      menuItem.classList.remove("active");
    });

    // Add active class to clicked item
    this.classList.add("active");

    // Update page title
    const section = this.getAttribute("data-section");
    const title = this.textContent.trim();
    document.getElementById("pageTitle").textContent = title;

    // Here you would load different content based on section
    loadSectionContent(section);
  });
});

function loadSectionContent(section) {
  const content = document.getElementById("dashboardContent");

  switch (section) {
    case "dashboard":
      // Already loaded by default
      break;
    case "ventas":
      content.innerHTML = `
                        <div class="stat-card">
                            <h2><i class="fas fa-cash-register"></i> Punto de Venta</h2>
                            <p>Módulo de ventas en desarrollo...</p>
                        </div>`;
      break;
    case "productos":
      content.innerHTML = `
                        <div class="stat-card">
                            <h2><i class="fas fa-box"></i> Gestión de Productos</h2>
                            <p>Módulo de productos en desarrollo...</p>
                        </div>`;
      break;
    default:
      content.innerHTML = `
                        <div class="stat-card">
                            <h2>Módulo: ${section}</h2>
                            <p>Este módulo está en desarrollo...</p>
                        </div>`;
  }
}

function logout() {
  sessionStorage.removeItem("currentUser");
  document.getElementById("dashboard").style.display = "none";
  document.getElementById("loginContainer").style.display = "block";
  document.getElementById("loginForm").reset();
  showAlert("Sesión cerrada exitosamente", "success");
}

// Check if user is already logged in
window.addEventListener("load", function () {
  const currentUser = sessionStorage.getItem("currentUser");
  if (currentUser) {
    const user = JSON.parse(currentUser);
    document.getElementById("loginContainer").style.display = "none";
    document.getElementById("dashboard").style.display = "block";
    document.getElementById("userInfo").textContent = `Usuario: ${user.nombre}`;
  }
});
