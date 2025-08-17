document.addEventListener("DOMContentLoaded", () => {
  loadCajas();
});

function loadCajas() {
    const caja = JSON.parse(localStorage.getItem("cajas")) || [];
    renderCajas(caja);
}

function renderCajas(cajas) {
  const contenedor = document.querySelector(".contenedor-caja");
  contenedor.innerHTML = "";

  if (cajas.length === 0) {
    contenedor.innerHTML = "<p>No hay cajas registradas.</p>";
    return;
  }

  cajas.forEach((caja) => {
    const div = document.createElement("div");
    div.classList.add("caja");

    div.innerHTML = `
      <h3>Caja #${caja.id}</h3>
      <p><strong>Fecha apertura:</strong> ${caja.fecha_apertura}</p>
      <p><strong>Estado:</strong> ${caja.estado}</p>
      <button class="btn btn-info" onclick="verCaja(${caja.id})">Ver detalles</button>
    `;

    contenedor.appendChild(div);
  });
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
    console.log(respuesta);
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