
function ObtenerProductos(){
    const clave = document.getElementById("clave");

    const id = clave.dataset.id;
    console.log(id);
    const data = {
        caja_id:id
    }
    $.post("./model/tasks/caja/getVentasTask.php",data,(response)=>{
        const respuesta = JSON.parse(response);
        console.log(respuesta);
        if(respuesta !== "bien"){
            clave.innerHTML = "No hay datos o ocurrio un error";
            clave.innerHTML +=  respuesta.mensaje;
            return;
        }
        clave.innerHTML = respuesta.data.map((datos)=>{
            ` <div>  ${datos} </div>`
        });
    });
}

ObtenerProductos();