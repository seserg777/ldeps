import { createApp } from 'vue'
// Import Vue components
// Feature components (barrel exports)
import { Categories, Category, ProductList, Product } from './features/catalog/components'
// Shared UI components (optional global reg)
import { BaseButton, UiCard } from './shared/components'
import UiModal from './shared/components/Ui/UiModal.vue'
import { MiniCart, MiniCartModal } from './features/cart/components'

// Make components globally available
// Bootstrap Vue apps and register components globally so they work in Blade in-DOM templates
function mountVueApps() {
  // Do not mount a blank app to #vue-root to avoid wiping server-rendered header
  // Components are mounted as "islands" below.
  const modalsRoot = document.getElementById('vue-root-modals')
  if (modalsRoot) {
    const modalsApp = createApp({})
    modalsApp.component('ui-modal', UiModal)
    modalsApp.component('mini-cart-modal', MiniCartModal)
    modalsApp.mount('#vue-root-modals')
  }
}

// Mount immediately if DOM is ready; also register as fallback
if (document.readyState === 'interactive' || document.readyState === 'complete') {
  mountVueApps()
} else {
  document.addEventListener('DOMContentLoaded', mountVueApps)
}

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
export { Categories, Category, ProductList, Product }

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