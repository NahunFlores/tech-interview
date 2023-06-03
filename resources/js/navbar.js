try {
    var subMenu = document.getElementById('subMenu')
    var fullPageMenu = document.getElementById('fullPageMenu')
    subMenu.addEventListener('click', function () {
        if (subMenu.className === 'menuClicked') {
            subMenu.className = ""
            setTimeout(function () { fullPageMenu.className = "visually-hidden" }, 200)
        } else {
            subMenu.className = 'menuClicked'
            fullPageMenu.className = "slideRight"
        }
    })


    // Obtener referencias a los elementos del DOM
    var dropdownBtn = document.getElementById('dropdownBtn');
    var dropdownMenu = document.getElementById('dropdownMenu');

    // Agregar evento de clic al botón desplegable
    dropdownBtn.addEventListener('click', function () {
        // Alternar la clase 'show' en el menú desplegable
        dropdownMenu.classList.toggle('show');
    });

    // Cerrar el menú desplegable cuando se hace clic fuera de él
    window.addEventListener('click', function (event) {
        if (!dropdownBtn.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });


} catch (error) {
    // Captura y maneja la excepción
    console.log('Se ha producido un error:', error);
}
