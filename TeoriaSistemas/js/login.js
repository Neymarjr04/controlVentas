/**
 * @typedef {{
 *  status:string,
 *  mensaje:string,
 *  data:Array
 * }} Response
 * 
 */
$(document).ready(function () {
  document.getElementById("loginForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const usuario = document.getElementById("usuario").value;
    const password = document.getElementById("password").value;
    const datos = {
      usuario,
      password,
    };
    $.post("./model/tasks/loginTask.php", datos, (response) => {
      /**@type {Response}  */
      const respuesta  = JSON.parse(response);
      if(respuesta.status !== "bien"){
        showAlert(respuesta.mensaje,"error");
      }
      
      $.post("./model/tasks/obtenerProductosTask.php",(response2)=>{
        const datos = JSON.parse(response2);
        let productos = [];
        datos.data.forEach((objeto)=>{
          let producto = {
            id:objeto.id,
            nombre:objeto.nombre,
            codigo_barras:objeto.codigo_barras,
            categoria_id:objeto.categoria_id,
            categoria_nombre:objeto.categoria_nombre,
            descripcion:objeto.descripcion,
            precio_compra:Number(objeto.precio_compra),
            precio_venta:Number(objeto.precio_venta),
            stock_actual:Number(objeto.stock_actual),
            stock_minimo:Number(objeto.stock_minimo),
            unidad_medida:objeto.unidad_medida,
            estado:objeto.estado,
            fecha_registro:Date(objeto.fecha_registro) 
          }
          productos.push(producto);
        })
        localStorage.setItem("products", JSON.stringify(productos));
      });

      $.post("./model/tasks/getCategoriasTask.php",(response)=>{
        const respuestaServer = JSON.parse(response);
        console.log("lleg aqui");
        if(respuestaServer.status !== "bien"){
          showAlert(respuesta.mensaje,"error");
        }
        localStorage.setItem("categorias",JSON.stringify(respuestaServer.data));
      });

      showAlert(respuesta.mensaje,"succes",true);
    });
  });
});

function showAlert(message, type,estado = false) {
  const alertContainer = document.getElementById("alertContainer");
  alertContainer.innerHTML = `<div class="alert alert-${type}">${message}</div>`;

  setTimeout(() => {
    alertContainer.innerHTML = "";  
    if(estado){
      location.reload(true);
    }
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

window.addEventListener("load", function () {
  if (currentUser) {
    const user = JSON.parse(currentUser);
    document.getElementById("loginContainer").style.display = "none";
    document.getElementById("dashboard").style.display = "block";
    document.getElementById("userInfo").textContent = `Usuario: ${user.nombre}`;
  }
});
