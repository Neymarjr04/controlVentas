
function sidebarControl(){
    
    
    const flechaMov = document.getElementsByClassName("flechaNavbar")[0];
    
    flechaMov.addEventListener("click",()=>{
        const sidebarMaster = document.getElementById("sidebarMaster");
        let sidebar = localStorage.getItem("sidebar");
        if(sidebar === 'true'){
            sidebarMaster.classList.remove("active");
            sidebarMaster.classList.add("desactive");
            localStorage.setItem("sidebar",false);
        }else{
            sidebarMaster.classList.remove("desactive");
            sidebarMaster.classList.add("active");
            localStorage.setItem("sidebar",true);
        }
    })
}

sidebarControl();