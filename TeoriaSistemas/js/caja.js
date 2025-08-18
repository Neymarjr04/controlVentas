let cajas = [{
  id:3,
  fecha_apertura:Date(),
  fecha_cierre:Date(),
  monto_inicial:12,
  estado:"abierta" | "cerrada",
  observaciones:"prueba",
  nombre:"prueba"
}];
document.addEventListener("DOMContentLoaded", () => {
  loadCajas();
  eventos();
});

function loadCajas() {
    cajas = JSON.parse(sessionStorage.getItem("cajas")) || [];
    renderCajas(cajas);
}

function renderCajas(cajas) {
  const contenedor = document.getElementById("productsTableBody");
  contenedor.innerHTML = "";

  if (cajas.length === 0) {
    contenedor.innerHTML = "<p>No hay cajas registradas.</p>";
    return;
  }
  console.log(cajas)
  contenedor.innerHTML = cajas.map((caja)=> productosLoad(caja));
}

function openProductModal() {
  const nombre = document.getElementById("nombreCaja");
  const montoInicial = document.getElementById("montoInicialCaja");
  const descripcion = document.getElementById("cajaDescription");
  
  const data = {
    nombre:nombre.value,
    montoInicial:montoInicial.value,
    descripcion:descripcion.value
  }

  $.post("./model/tasks/caja/createCajaTask.php", data,(response)=>{
    const respuesta = JSON.parse(response);
    if(respuesta.status !== 'bien'){
      alertMessage("ocurrio un error !",respuesta.mensaje,"error");
      return;
    }
    cajas.push(respuesta.data[0]);
    localStorage.setItem("categorias",cajas);
    console.log(respuesta);
  });
  
}
function cerrarCaja(id){
  const product = cajas.find((p) => p.id === id);
  Swal.fire({
    title: "Estas seguro ?",
    text: "Una ves cerrado no se puede volver a habilitar",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "si ,Cerrar ! "
  }).then((result) => {
    if (result.isConfirmed) {
      const data = {
        idCaja:id
      }
      $.post("./model/tasks/caja/updateCajaTask.php",data,(response)=>{
        const respuesta = JSON.parse(response);
        if(respuesta.status == 'bien'){
          product.estado = "cerrada";
          sessionStorage.setItem("cajas",JSON.stringify(cajas));
          alertMessage("Felicitaciones",respuesta.mensaje,"success");
          renderCajas(cajas);
          return;
        }
        alertMessage("Ups ! ", respuesta.mensaje,"error");
      })
    }
  });
}

function verCaja(id) {
  window.location.href = `detalle_caja.php?id=${id}`;
}

function closeProductModal() {
  document.getElementById("productModal").style.display = "none";
}

function openCajaModal(){
  const modal = document.getElementById("productModal");
  modal.style.display = "block";
}

function eventos(){
  const formulario = document.getElementById("productForm");

  formulario.addEventListener("submit",(e)=>{
    e.preventDefault();
    openProductModal();
  });
}

/**
 * @typedef { "question" | "error" | "success" | "info" | "warning" } datos 
 */
/**
 * 
 * @param {string} titulo 
 * @param {string} descripcion 
 * @param {datos} icono 
 */
function alertMessage(titulo,descripcion,icono){
  Swal.fire({
    title: titulo,
    text: descripcion,
    icon: icono
  });
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
                                  <h4>${product.nombre }</h4>
                                  <p>${
                                    product.observaciones || "Sin descripción"
                                  }</p>
                              </div>
                          </div>
                      </td>
                      <td>${product.estado}</td>
                      <td>${product.fecha_apertura}</td>
                      <td class="price-cell">${product.fecha_cierre || "No establecido"}  </td>
                      <td class="price-cell">S/ ${product.monto_inicial}  </td>
                      <td class="price-cell">S/ ${product.monto_final || "0"}  </td>
                      <td>
                        <div class="action-buttons">
                          <a href="/caja?idCaja=${product.id}" class="btn btn-info" > Ver detalles </a>
                          ${ product.estado === 'abierta' ? 
                          `<button class="btn btn-danger btn-sm" onclick="cerrarCaja(${
                            product.id
                          })" title="Cambiar Estado">
                              <i class="fas fa-power-off"></i>
                          </button>`:""
                          }
                        
                        </div>
                        
                      </td>
                  </tr>
              `;
  return datos;
}