
const mobileMenuBtn = document.querySelector('#mobile-menu');
const cerrarMenuBtn = document.querySelector('#cerrar-menu');
const sideBar = document.querySelector('.sidebar');

if(mobileMenuBtn){
    mobileMenuBtn.addEventListener('click', mostrarSideBar);
}

if(cerrarMenuBtn){
    cerrarMenuBtn.addEventListener('click', cerrarSideBar);
}

function mostrarSideBar(){
    sideBar.classList.add('mostrar');
}

function cerrarSideBar(){
    sideBar.classList.add('ocultar');

    setTimeout(() => {
        sideBar.classList.remove('mostrar');
        sideBar.classList.remove('ocultar');
    }, 1000);
}

// Eliminar clase mostrar en pantallas grandes (tablet / desktop)
const anchoPantalla = document.body.clientWidth;

window.addEventListener('resize', () => {
    const anchoPantalla = document.body.clientWidth;

    if(anchoPantalla >= 768){
        sideBar.classList.remove('mostrar');
    }
});


