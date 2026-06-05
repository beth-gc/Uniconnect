<footer class="pie">
    <p>Desarrollado por Elizabeth Gomez &copy; <?php echo date('Y'); ?></p>
</footer>
<script>
// Esperar a que todo cargue completamente (incluyendo el security check de InfinityFree)
window.addEventListener('load', function() {
    // Menú hamburguesa
    var btnHamburguesa = document.getElementById('btn-hamburguesa');
    var navMenu = document.getElementById('nav-menu');

    if (btnHamburguesa) {
        btnHamburguesa.addEventListener('click', function() {
            navMenu.classList.toggle('menu_abierto');
            btnHamburguesa.classList.toggle('hamburguesa_activa');
        });
    }

    // Animaciones de entrada
    var elementos = document.querySelectorAll('.animacion-entrada');
    
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        elementos.forEach(function(el) {
            observer.observe(el);
        });
    } else {
        // Fallback: mostrar todo si IntersectionObserver no está disponible
        elementos.forEach(function(el) {
            el.classList.add('visible');
        });
    }
});
</script>
