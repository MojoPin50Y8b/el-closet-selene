import './bootstrap';
import '../css/app.css';
import 'alpinejs';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

/* ---------------- helpers ---------------- */
function debounce(fn, wait = 300) {
  let t;
  return (...args) => {
    clearTimeout(t);
    t = setTimeout(() => fn(...args), wait);
  };
}

function getCsrfToken() {
  const meta = document.querySelector('meta[name="csrf-token"]');
  return meta?.getAttribute('content') ?? '';
}

async function postJSON(url, payload) {
  const token = getCsrfToken();
  if (!token) throw new Error('CSRF token ausente en <meta name="csrf-token">');
  const res = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': token,
      'Accept': 'application/json'
    },
    body: JSON.stringify(payload)
  });
  if (!res.ok) {
    const text = await res.text().catch(() => '');
    throw new Error(`Request failed (${res.status}) ${text}`);
  }
  try { return await res.json(); } catch { return {}; }
}

/* --------------- search autocomplete --------------- */
function setupSearchAutocomplete() {
  const input = document.getElementById('search-input');
  const panel = document.getElementById('search-panel');
  const list  = document.getElementById('search-results');
  if (!input || !panel || !list) return;

  const close = () => panel.classList.add('hidden');

  const render = (items) => {
    list.innerHTML = '';
    if (!items.length) {
      list.innerHTML = `<li class="px-4 py-3 text-sm text-gray-500">Sin resultados</li>`;
      return;
    }
    for (const item of items) {
      const li = document.createElement('li');
      li.className = 'px-3 py-2 hover:bg-gray-50';
      li.innerHTML = `
        <a class="flex items-center gap-3" href="/producto/${encodeURIComponent(item.slug)}">
          <img src="${item.thumb ?? ''}" class="w-10 h-12 object-cover rounded bg-gray-100" onerror="this.style.display='none'">
          <span>${item.name}</span>
        </a>`;
      list.appendChild(li);
    }
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
    } catch {
      close();
    }
  }, 250);

  input.addEventListener('input', e => search(e.target.value));
  input.addEventListener('focus', () => {
    if (list.children.length) panel.classList.remove('hidden');
  });
  document.addEventListener('click', (e) => {
    if (!panel.contains(e.target) && e.target !== input) close();
  });
  input.addEventListener('keydown', (e) => (e.key === 'Escape') && close());
}

/* ---------------- cart count ---------------- */
async function fetchCartCount() {
  try {
    const res = await fetch('/cart/count', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();
    const el = document.getElementById('cart-count');
    if (el) el.textContent = (json.count ?? 0);
  } catch {}
}
function setupCartCount() {
  fetchCartCount();
  window.addEventListener('cart:updated', fetchCartCount);
}
window.notifyCartUpdated = () => window.dispatchEvent(new Event('cart:updated'));

/* --------------- add / remove cart --------------- */
function setupAddToCart() {
  document.querySelectorAll('.js-add-to-cart').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();

      const url = btn.dataset.url;
      const product_id = Number(btn.dataset.product);
      let   variant_id = btn.dataset.variant ? Number(btn.dataset.variant) : null;
      let   qty        = Number(btn.dataset.qty || 1);

      // opcional: lee selectores si existen
      const vSel = btn.dataset.variantEl ? document.querySelector(btn.dataset.variantEl) : null;
      const qSel = btn.dataset.qtyEl ? document.querySelector(btn.dataset.qtyEl) : null;
      if (vSel?.value) variant_id = Number(vSel.value);
      if (qSel?.value) qty = Number(qSel.value);

      try {
        await postJSON(url, { product_id, variant_id, qty });
        window.notifyCartUpdated();
        btn.classList.add('opacity-70');
        setTimeout(() => btn.classList.remove('opacity-70'), 500);
      } catch (err) {
        console.error(err);
        alert('No se pudo añadir al carrito.');
      }
    });
  });
}

function setupRemoveFromCart() {
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-remove-from-cart]');
    if (!btn) return;
    e.preventDefault();

    const url = btn.getAttribute('data-url') || '/cart/remove';
    const id  = btn.getAttribute('data-id');
    try {
      await postJSON(url, { id });
      window.notifyCartUpdated();
      btn.closest('[data-cart-row]')?.remove();
    } catch (err) {
      console.error(err);
    }
  });
}

/* --------------- mini cart (panel con HTML) --------------- */
function setupMiniCart() {
  const root  = document.getElementById('mini-cart-root');
  const btn   = document.getElementById('mini-cart-btn') || root?.querySelector('a[href$="/carrito"], a[title="Carrito"]');
  const panel = document.getElementById('mini-cart-panel');
  if (!root || !btn || !panel) return;

  async function loadMini() {
    try {
      const res = await fetch('/cart/mini', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const html = await res.text();
      panel.innerHTML = html;
    } catch {}
  }

  const toggle = async (e) => {
    // si quieres que el click abra el panel en vez de navegar
    e.preventDefault();
    if (panel.classList.contains('hidden')) { await loadMini(); panel.classList.remove('hidden'); }
    else { panel.classList.add('hidden'); }
  };

  btn.addEventListener('click', toggle);

  document.addEventListener('click', (e) => {
    if (!root.contains(e.target)) panel.classList.add('hidden');
  });

  // Si se actualiza el carrito y el panel está abierto, refrescar
  window.addEventListener('cart:updated', async () => {
    if (!panel.classList.contains('hidden')) await loadMini();
  });
}

/* ---------------- boot ---------------- */
document.addEventListener('DOMContentLoaded', () => {
  setupSearchAutocomplete();
  setupCartCount();
  setupAddToCart();
  setupRemoveFromCart();
  setupMiniCart(); // <- NUEVO
});
