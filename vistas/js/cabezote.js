document.addEventListener('DOMContentLoaded', function() {
    var url = window.location.href; // Obtiene el URL completo
    var palabraBuscada1 = "inicio"; // La palabra que quieres buscar
    var palabraBuscada2 = "vehiculos"; // La palabra que quieres buscar
    var palabraBuscada3 = "blog"; // La palabra que quieres buscar
    var palabraBuscada4 = "resenas"; // La palabra que quieres buscar
    var palabraBuscada5 = "contacto"; // La palabra que quieres buscar
    var palabraBuscada6 = "vuelos"; // La palabra que quieres buscar

    if (url.includes(palabraBuscada1)) {
        document.getElementById('idinicio').classList.add('active-link');
    } else if (url.includes(palabraBuscada2)) {
        document.getElementById('idvehiculos').classList.add('active-link');
    } else if (url.includes(palabraBuscada3)) {
        document.getElementById('idblog').classList.add('active-link');
    } else if (url.includes(palabraBuscada4)) {
        document.getElementById('idresenas').classList.add('active-link');
    } else if (url.includes(palabraBuscada5)) {
        document.getElementById('idcontacto').classList.add('active-link');
    } else if (url.includes(palabraBuscada6)) {
        document.getElementById('idvuelos').classList.add('active-link');
    }
});