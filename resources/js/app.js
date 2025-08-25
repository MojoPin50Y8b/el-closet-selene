import './bootstrap';
import '../css/app.css';
import 'alpinejs'

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// --- helpers ---
function debounce(fn, wait = 300) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn(...args), wait);
    };
}

// --- SEARCH AUTOCOMPLETE ---
function setupSearchAutocomplete() {
    const input = document.getElementById('search-input');
    const panel = document.getElementById('search-panel');
    const list = document.getElementById('search-results');

    if (!input || !panel || !list) return;

    const close = () => panel.classList.add('hidden');

    const render = (items) => {
        list.innerHTML = '';
        if (!items.length) {
            list.innerHTML = `<li class="px-4 py-3 text-sm text-gray-500">Sin resultados</li>`;
            return;
        }
        items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'px-3 py-2 hover:bg-gray-50';
            li.innerHTML = `
                <a class="flex items-center gap-3" href="/producto/${encodeURIComponent(item.slug)}">
                    <img src="${item.thumb ?? ''}" class="w-10 h-12 object-cover rounded bg-gray-100" onerror="this.style.display='none'">
                    <span>${item.name}</span>
                </a>
            `;
            list.appendChild(li);
        });
    };

    const search = debounce(async (q) => {
        if (!q) { close(); return; }
        try {
            const res = await fetch(`/search/suggest?q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            render(json.data ?? []);
            panel.classList.remove('hidden');
        } catch (e) {
            close();
        }
    }, 250);

    input.addEventListener('input', e => search(e.target.value));
    input.addEventListener('focus', e => {
        if (list.children.length) panel.classList.remove('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!panel.contains(e.target) && e.target !== input) close();
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
}

// --- CART COUNT ---
async function fetchCartCount() {
    try {
        const res = await fetch('/cart/count', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const json = await res.json();
        const el = document.getElementById('cart-count');
        if (el) el.textContent = (json.count ?? 0);
    } catch { }
}

function setupCartCount() {
    fetchCartCount();
    // si otras partes del sitio disparan 'cart:updated', actualiza el contador
    window.addEventListener('cart:updated', fetchCartCount);
}

document.addEventListener('DOMContentLoaded', () => {
    setupSearchAutocomplete();
    setupCartCount();
});

// --- helper global para notificar ---
window.notifyCartUpdated = () => {
    window.dispatchEvent(new Event('cart:updated'));
};

// --- interceptar "Añadir al carrito" via AJAX ---
function setupAddToCart() {
    document.addEventListener('submit', async (e) => {
        if (!e.target.matches('.js-add-to-cart')) return;
        e.preventDefault();

        const form = e.target;
        const action = form.getAttribute('action') || '/cart/add';
        const formData = new FormData(form);

        try {
            const res = await fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content') ?? ''
                },
                body: formData
            });

            if (!res.ok) throw new Error('Add to cart failed');

            // Notifica al header que refresque el badge
            window.notifyCartUpdated();

            // (opcional) feedback visual
            form.querySelector('[type="submit"]')?.classList.add('opacity-70');
            setTimeout(() => form.querySelector('[type="submit"]')?.classList.remove('opacity-70'), 800);
        } catch (err) {
            console.error(err);
            // (opcional) mostrar toast/error
        }
    });
}

// --- interceptar "Quitar del carrito" via AJAX ---
function setupRemoveFromCart() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-remove-from-cart]');
        if (!btn) return;

        e.preventDefault();

        const url = btn.getAttribute('data-url') || '/cart/remove';
        const payload = {
            id: btn.getAttribute('data-id') // el id/rowId/variant a eliminar
        };

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content') ?? ''
                },
                body: JSON.stringify(payload)
            });

            if (!res.ok) throw new Error('Remove failed');

            // refresca contador
            window.notifyCartUpdated();

            // (opcional) eliminar fila del mini-carrito en el DOM
            const row = btn.closest('[data-cart-row]');
            row?.remove();
        } catch (err) {
            console.error(err);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // ya tenías:
    // setupSearchAutocomplete();
    // setupCartCount();

    // añade:
    setupAddToCart();
    setupRemoveFromCart();
});
