<footer class="bg-gray-100 text-sm">
    <div class="max-w-7xl mx-auto px-4 py-10 grid gap-6 md:grid-cols-4">
        <div>
            <h4 class="font-semibold mb-2">Compañía</h4>
            <ul>
                <li><a href="#">Sobre nosotros</a></li>
                <li><a href="#">Contacto</a></li>
                <li><a href="#">Blog</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-semibold mb-2">Ayuda</h4>
            <ul>
                <li><a href="#">Envíos y devoluciones</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Privacidad</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-semibold mb-2">Newsletter</h4>
            <form class="flex gap-2">
                <input class="border px-3 py-2 w-full" placeholder="Tu email">
                <button class="btn-primary px-4 py-2 rounded">Suscribirme</button>
            </form>
        </div>
        <div class="text-gray-500">© {{ date('Y') }} El Clóset de Selene</div>
    </div>
</footer>