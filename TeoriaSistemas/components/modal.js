
console.log("modal conectado");

class ModalControl{
  #elemento = document.createElement("div");
  #tiempoCerrado = 1;
  #modal1 = `
      <div class="modal-cuerpo">
          <div class="modal-titulo">
              <p class="titulo-modal" >Este es un titulo</p>
              <p class="boton-cerrar">x</p>
          </div>
          <div class="modal-contenido">

          </div>
          <div class="modal-footer1">
          </div>
      </div>
  `;
  
  constructor({modal = 1,id = "modalMaster", tiempoCerrado = 1} = {} ){
    this.#elemento.id = id;
    this.#elemento.setAttribute("class","modal-fondo");
    this.#tiempoCerrado = tiempoCerrado;
    if(modal == 1){
      this.#elemento.innerHTML = this.#modal1;
    }
    document.body.appendChild( this.#elemento);

    this.#eventosModal();
  }


  setContenidoModal(titulo,contenido)
  {
    const titulo1 = this.#elemento.querySelector(".titulo-modal");
    const contenedor = this.#elemento.querySelector(".modal-contenido")
    titulo1.innerHTML = titulo;
    if(contenido !== undefined ){
      contenedor.innerHTML = "";
      contenedor.appendChild(contenido);
    }
  }
  setContenidoFooter(contenido){
    const contenedor = this.#elemento.querySelector(".modal-contenido");
    if(contenido !== undefined ){
      contenedor.appendChild(contenido);
    }
  }

  activarModal(){
    this.#elemento.classList.add("animacionAparecer");
    this.#elemento.style.display = "flex";
    setTimeout(()=>{
      this.#elemento.classList.remove("animacionAparecer");
    },this.#tiempoCerrado * 1000);

  }

  #eventosModal(){
    const fondo = this.#elemento;
    const botonCerrar = this.#elemento.querySelector(".boton-cerrar");
    const modalCUerpo = this.#elemento.querySelector(".modal-cuerpo");

    fondo.addEventListener("click",()=> this.cerrarEvento());
    botonCerrar.addEventListener("click",()=> this.cerrarEvento());
    modalCUerpo.addEventListener("click",(e)=>{
      e.stopPropagation();
    });
  }

  cerrarEvento(){
    this.#elemento.classList.add("animacionCerradoModal");
    setTimeout(()=>{
      this.#elemento.style.display = "none";
      this.#elemento.classList.remove("animacionCerradoModal");
      ModalControl.agregarScroll();
    },this.#tiempoCerrado * 1000);
  }

  static quitarScroll = ()=>{
    document.body.classList.add("no-scroll");  
  }

  static agregarScroll = ()=>{
      document.body.classList.remove("no-scroll");
  }

}