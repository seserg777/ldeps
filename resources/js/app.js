import { createApp } from 'vue'

// App.js loaded successfully
// Global styles (Tailwind for admin and shared UI)
import '../css/app.css'
// Import Vue components
// Feature components (barrel exports)
import { Categories, Category, ProductList, Product, ProductsModule, SearchModule } from './features/catalog/components'
// Shared UI components (optional global reg)
import { BaseButton, UiCard } from './shared/components'
import UiModal from './shared/components/Ui/UiModal.vue'
import { MiniCart, MiniCartModal, CartModal } from './features/cart/components'
import { AuthButton } from './features/auth/components'
import { Menu as SiteMenu } from './features/menu/components'
import { Homepage } from './features/homepage/components'
import { Page } from './features/page/components'
import { Content, Article, ArticleList } from './features/content/components'
import { Exussalebanner, ExussalebannerList } from './features/promo/components'
import { Pagination } from './features/common/components'

// Make components globally available
// Bootstrap Vue apps and register components globally so they work in Blade in-DOM templates
function mountVueApps() {
  // Do not mount a blank app to #vue-root to avoid wiping server-rendered header
  // Components are mounted as "islands" below.
  const modalsRoot = document.getElementById('vue-root-modals')
  if (modalsRoot) {
    const modalsApp = createApp({ render: () => null })
    modalsApp.component('ui-modal', UiModal)
    modalsApp.component('mini-cart-modal', MiniCartModal)
    modalsApp.component('cart-modal', CartModal)
    modalsApp.mount('#vue-root-modals')
  }
}

// Mount immediately if DOM is ready; also register as fallback
// mountVueApps() is now called in the general mounting block

// Global cart functions
window.addToCart = function(productId) {
    const button = document.querySelector(`[data-product-id="${productId}"]`);
    if (!button) return;

    const originalHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('btn-primary');
            button.classList.add('btn-success');
            
            // Update cart count
            updateCartCount();
            
            // Open cart modal if available
            if (window.cartModalInstance) {
                window.cartModalInstance.open();
            }
            
            setTimeout(() => {
                button.innerHTML = originalHtml;
                button.classList.remove('btn-success');
                button.classList.add('btn-primary');
                button.disabled = false;
            }, 2000);
        } else {
            button.innerHTML = originalHtml;
            button.disabled = false;
            console.error('Error adding to cart:', data.message);
        }
    })
    .catch(error => {
        button.innerHTML = originalHtml;
        button.disabled = false;
        console.error('Error:', error);
    });
}

window.addToWishlist = function(productId) {
    const button = document.querySelector(`[data-product-id="${productId}"]`);
    if (!button) return;

    const originalHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('/wishlist/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('btn-outline-danger');
            button.classList.add('btn-success');
            
            // Update wishlist count
            updateWishlistCount();
            
            setTimeout(() => {
                button.innerHTML = originalHtml;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-danger');
                button.disabled = false;
            }, 2000);
        } else {
            button.innerHTML = originalHtml;
            button.disabled = false;
            console.error('Error adding to wishlist:', data.message);
        }
    })
    .catch(error => {
        button.innerHTML = originalHtml;
        button.disabled = false;
        console.error('Error:', error);
    });
}

function updateCartCount() {
    fetch('/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement && data.count !== undefined) {
                cartCountElement.textContent = data.count;
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}

function updateWishlistCount() {
    fetch('/wishlist/count')
        .then(response => response.json())
        .then(data => {
            const wishlistCountElement = document.getElementById('wishlist-count');
            if (wishlistCountElement && data.count !== undefined) {
                wishlistCountElement.textContent = data.count;
            }
        })
        .catch(error => console.error('Error updating wishlist count:', error));
}

// Export for module systems
export { Categories, Category, ProductList, Product, ProductsModule, SearchModule, Homepage, Page, Content, Article, ArticleList, Exussalebanner, ExussalebannerList, Pagination }

// Delegated click handler for buttons with .add-to-cart
document.addEventListener('click', (e) => {
  const btn = e.target && e.target.closest && e.target.closest('.add-to-cart')
  if (btn) {
    e.preventDefault()
    const productId = btn.getAttribute('data-product-id')
    if (productId) {
      window.addToCart(productId)
    }
  }
})

// Progressive enhancement: mount MiniCart on standalone tags if template compiler didn't process in-DOM templates
function mountMiniCartIslands() {
  const nodes = document.querySelectorAll('mini-cart')
  nodes.forEach((el) => {
    // Skip if already has a Vue instance (heuristic)
    if (el.__vue_app__) return
    const props = {
      cartIndexUrl: el.getAttribute('cart-index-url'),
      cartModalUrl: el.getAttribute('cart-modal-url'),
      cartRemoveUrl: el.getAttribute('cart-remove-url'),
      csrfToken: el.getAttribute('csrf-token'),
      countUrl: el.getAttribute('count-url'),
      useFloat: el.getAttribute('use-float') === 'true'
    }
    try {
      const app = createApp(MiniCart, props)
      app.mount(el)
      el.__vue_app__ = app
    } catch (e) {
      // noop
    }
  })
}

if (document.readyState === 'interactive' || document.readyState === 'complete') {
  mountMiniCartIslands()
} else {
  document.addEventListener('DOMContentLoaded', mountMiniCartIslands)
}

// Mount AuthButton islands
function mountAuthIslands() {
  const nodes = document.querySelectorAll('auth-button')
  nodes.forEach((el) => {
    if (el.__vue_app__) return
    const props = {
      isAuthenticated: el.getAttribute('is-authenticated') === 'true',
      username: el.getAttribute('username') || '',
      loginUrl: el.getAttribute('login-url'),
      logoutUrl: el.getAttribute('logout-url'),
      csrfToken: el.getAttribute('csrf-token')
    }
    try {
      const app = createApp(AuthButton, props)
      app.mount(el)
      el.__vue_app__ = app
    } catch (e) {}
  })
}

if (document.readyState === 'interactive' || document.readyState === 'complete') {
  mountAuthIslands()
} else {
  document.addEventListener('DOMContentLoaded', mountAuthIslands)
}

// Mount CartModal islands
function mountCartModalIslands() {
  const nodes = document.querySelectorAll('cart-modal')
  nodes.forEach((el) => {
    if (el.__vue_app__) return
    const props = {
      cartIndexUrl: el.getAttribute('cart-index-url'),
      cartModalUrl: el.getAttribute('cart-modal-url'),
      cartRemoveUrl: el.getAttribute('cart-remove-url'),
      csrfToken: el.getAttribute('csrf-token')
    }
    try {
      const app = createApp(CartModal, props)
      app.mount(el)
      el.__vue_app__ = app
    } catch (e) {}
  })
}

// mountCartModalIslands() is now called in the general mounting block

// SiteMenu is only used inside Vue components now; no in-DOM mounting needed

// Mount Homepage islands
function mountHomepageIslands() {
  const nodes = document.querySelectorAll('homepage-component')
  console.log('Mounting homepage islands, found nodes:', nodes.length)
  nodes.forEach((el) => {
    if (el.__vue_app__) {
      console.log('Homepage already mounted, skipping')
      return
    }
    const props = {
      menuItems: (() => {
        const raw = el.getAttribute('menu-items')
        if (!raw) return []
        try { return JSON.parse(raw) } catch { return [] }
      })(),
      language: el.getAttribute('language') || 'uk',
      siteName: el.getAttribute('site-name') || '',
      siteDescription: el.getAttribute('site-description') || ''
    }
    console.log('Mounting homepage with props:', props)
    try {
      const app = createApp(Homepage, props)
      app.mount(el)
      el.__vue_app__ = app
      console.log('Homepage mounted successfully')
    } catch (e) {
      console.error('Error mounting homepage:', e)
    }
  })
}
// Mount Page islands (wrapper for internal pages; renders slot content)
function mountPageIslands() {
  const nodes = document.querySelectorAll('page-component')
  console.log('Mounting page islands, found nodes:', nodes.length)
  nodes.forEach((el) => {
    if (el.__vue_app__) return
    const props = {
      language: el.getAttribute('language') || 'uk',
      siteName: el.getAttribute('site-name') || '',
      siteDescription: el.getAttribute('site-description') || '',
      title: el.getAttribute('title') || '',
      menuItem: (() => {
        const raw = el.getAttribute('menu-item')
        if (!raw) return null
        try { return JSON.parse(raw) } catch { return null }
      })(),
      linkParams: (() => {
        const raw = el.getAttribute('link-params')
        if (!raw) return null
        try { return JSON.parse(raw) } catch { return null }
      })(),
      article: (() => {
        const raw = el.getAttribute('article')
        if (!raw) return null
        try { return JSON.parse(raw) } catch { return null }
      })(),
      articles: (() => {
        const raw = el.getAttribute('articles')
        if (!raw) return []
        try { return JSON.parse(raw) } catch { return [] }
      })(),
      pagination: (() => {
        const raw = el.getAttribute('pagination')
        if (!raw) return null
        try { return JSON.parse(raw) } catch { return null }
      })()
    }
    try {
      const app = createApp(Page, props)
      // Register possible child components used inside slot content
      app.component('content-component', Content)
      app.component('content-list-component', ArticleList)
      app.component('exussalebanner-component', Exussalebanner)
      app.component('exussalebanner-list-component', ExussalebannerList)
      app.component('pagination-component', Pagination)
      app.mount(el)
      el.__vue_app__ = app
      console.log('Page mounted successfully')
    } catch (e) {
      console.error('Error mounting page:', e)
    }
  })
}

// Mount ProductsModule islands
function mountProductsModuleIslands() {
  const nodes = document.querySelectorAll('products-module')
  nodes.forEach((el) => {
    if (el.__vue_app__) return
    const props = {
      type: el.getAttribute('type') || 'random',
      limit: Number(el.getAttribute('limit') || 3) || 3,
      apiUrl: el.getAttribute('api-url') || '/api/products'
    }
    try {
      const app = createApp(ProductsModule, props)
      app.mount(el)
      el.__vue_app__ = app
    } catch (e) {}
  })
}

// Mount SearchModule islands
function mountSearchModuleIslands() {
  const nodes = document.querySelectorAll('search-module')
  nodes.forEach((el) => {
    if (el.__vue_app__) return
    const props = {
      apiUrl: el.getAttribute('api-url') || '/api/search',
      placeholder: el.getAttribute('placeholder') || 'Поиск оборудования'
    }
    try {
      const app = createApp(SearchModule, props)
      app.mount(el)
      el.__vue_app__ = app
    } catch (e) {}
  })
}


// mountHomepageIslands() is now called in the general mounting block

// Content is rendered inside Page/Homepage via Content wrapper; no islands needed

// Content list is handled inside Content; no islands needed

// Promo components are rendered inside Content; no islands needed

// Promo list handled inside Content; no islands needed

// Mount all islands
if (document.readyState === 'interactive' || document.readyState === 'complete') {
  mountVueApps()
  mountCartModalIslands()
  mountPageIslands()
  mountHomepageIslands()
  mountProductsModuleIslands()
  mountSearchModuleIslands()
} else {
  document.addEventListener('DOMContentLoaded', () => {
    mountVueApps()
    mountCartModalIslands()
    mountPageIslands()
    mountHomepageIslands()
    mountProductsModuleIslands()
    mountSearchModuleIslands()
  })
}