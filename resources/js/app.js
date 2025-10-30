import { createApp } from 'vue'

// App.js loaded successfully
// Global styles (Tailwind for admin and shared UI)
import '../css/app.css'
// Import Vue components
// Feature components (barrel exports)
import { Categories, Category, ProductList, Product } from './features/catalog/components'
// Shared UI components (optional global reg)
import { BaseButton, UiCard } from './shared/components'
import UiModal from './shared/components/Ui/UiModal.vue'
import { MiniCart, MiniCartModal, CartModal } from './features/cart/components'
import { AuthButton } from './features/auth/components'
import { Menu as SiteMenu } from './features/menu/components'
import { Homepage } from './features/homepage/components'
import { Content, ContentList } from './features/content/components'
import { Exussalebanner, ExussalebannerList } from './features/promo/components'
import { Pagination } from './features/common/components'

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
export { Categories, Category, ProductList, Product, Homepage, Content, ContentList, Exussalebanner, ExussalebannerList, Pagination }

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

// Mount SiteMenu islands
function mountSiteMenuIslands() {
  console.log('Vue available:', typeof createApp !== 'undefined')
  console.log('SiteMenu component:', SiteMenu)
  const nodes = document.querySelectorAll('site-menu')
  console.log('Mounting site-menu islands, found nodes:', nodes.length)
  nodes.forEach((el) => {
    if (el.__vue_app__) {
      console.log('Site-menu already mounted, skipping')
      return
    }
    const menuDataScript = el.querySelector('script.menu-data')
    let items = []
    if (menuDataScript && menuDataScript.textContent) {
      try {
        items = JSON.parse(menuDataScript.textContent)
        console.log('Site-menu: Parsed items from script tag:', items.length)
      } catch (e) {
        console.error('Site-menu: Error parsing menu data from script tag:', e)
      }
    }
    const props = {
      menutype: el.getAttribute('menutype') || 'top',
      apiUrl: el.getAttribute('api-url') || '',
      items: items, // Pass parsed items directly
      layout: el.getAttribute('layout') || 'default',
      language: el.getAttribute('language') || 'ru-UA'
    }
    console.log('Mounting site-menu with props:', props)
    try {
      const app = createApp(SiteMenu, props)
      app.mount(el)
      el.__vue_app__ = app
      console.log('Site-menu mounted successfully')
    } catch (e) {
      console.error('Error mounting site-menu:', e)
    }
  })
}

// mountSiteMenuIslands() is now called in the general mounting block

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
      heroData: (() => {
        const raw = el.getAttribute('hero-data')
        if (!raw) return {}
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid hero-data JSON for homepage:', e)
          return {}
        }
      })(),
      featuredCategories: (() => {
        const raw = el.getAttribute('featured-categories')
        if (!raw) return []
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid featured-categories JSON for homepage:', e)
          return []
        }
      })(),
      featuredProducts: (() => {
        const raw = el.getAttribute('featured-products')
        if (!raw) return []
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid featured-products JSON for homepage:', e)
          return []
        }
      })(),
      saleBanners: (() => {
        const raw = el.getAttribute('sale-banners')
        if (!raw) return []
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid sale-banners JSON for homepage:', e)
          return []
        }
      })(),
      loading: el.getAttribute('loading') === 'true',
      error: el.getAttribute('error') || null
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

// mountHomepageIslands() is now called in the general mounting block

// Mount Content islands
function mountContentIslands() {
  const nodes = document.querySelectorAll('content-component')
  console.log('Mounting content islands, found nodes:', nodes.length)
  nodes.forEach((el) => {
    if (el.__vue_app__) {
      console.log('Content already mounted, skipping')
      return
    }
    const props = {
      menuItems: (() => {
        const raw = el.getAttribute('menu-items')
        if (!raw) return []
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid menu-items JSON for content:', e)
          return []
        }
      })(),
      language: el.getAttribute('language') || 'ru-UA',
      siteName: el.getAttribute('site-name') || '',
      siteDescription: el.getAttribute('site-description') || '',
      menuItem: (() => {
        const raw = el.getAttribute('menu-item')
        if (!raw) return null
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid menu-item JSON for content:', e)
          return null
        }
      })(),
      linkParams: (() => {
        const raw = el.getAttribute('link-params')
        if (!raw) return null
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid link-params JSON for content:', e)
          return null
        }
      })(),
      article: (() => {
        const raw = el.getAttribute('article')
        if (!raw) return null
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid article JSON for content:', e)
          return null
        }
      })()
    }
    console.log('Mounting content with props:', props)
    try {
      const app = createApp(Content, props)
      app.mount(el)
      el.__vue_app__ = app
      console.log('Content mounted successfully')
    } catch (e) {
      console.error('Error mounting content:', e)
    }
  })
}

// mountContentIslands() is now called in the general mounting block

// Mount ContentList islands
function mountContentListIslands() {
  const nodes = document.querySelectorAll('content-list-component')
  console.log('Mounting content-list islands, found nodes:', nodes.length)
  nodes.forEach((el) => {
    if (el.__vue_app__) {
      console.log('ContentList already mounted, skipping')
      return
    }
    const props = {
      menuItems: (() => {
        const raw = el.getAttribute('menu-items')
        if (!raw) return []
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid menu-items JSON for content-list:', e)
          return []
        }
      })(),
      language: el.getAttribute('language') || 'uk',
      siteName: el.getAttribute('site-name') || '',
      siteDescription: el.getAttribute('site-description') || '',
      menuItem: (() => {
        const raw = el.getAttribute('menu-item')
        if (!raw) return null
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid menu-item JSON for content-list:', e)
          return null
        }
      })(),
      linkParams: (() => {
        const raw = el.getAttribute('link-params')
        if (!raw) return null
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid link-params JSON for content-list:', e)
          return null
        }
      })(),
      articles: (() => {
        const raw = el.getAttribute('articles')
        if (!raw) return []
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid articles JSON for content-list:', e)
          return []
        }
      })(),
      pagination: (() => {
        const raw = el.getAttribute('pagination')
        if (!raw) return null
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid pagination JSON for content-list:', e)
          return null
        }
      })()
    }
    console.log('Mounting content-list with props:', props)
    try {
      const app = createApp(ContentList, props)
      app.mount(el)
      el.__vue_app__ = app
      console.log('ContentList mounted successfully')
    } catch (e) {
      console.error('Error mounting content-list:', e)
    }
  })
}
// mountContentListIslands() is now called in the general mounting block

// Mount Exussalebanner islands
function mountExussalebannerIslands() {
  const nodes = document.querySelectorAll('exussalebanner-component')
  console.log('Mounting exussalebanner islands, found nodes:', nodes.length)
  nodes.forEach((el) => {
    if (el.__vue_app__) {
      console.log('Exussalebanner already mounted, skipping')
      return
    }
    const props = {
      menuItems: (() => {
        const raw = el.getAttribute('menu-items')
        if (!raw) return []
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid menu-items JSON for exussalebanner:', e)
          return []
        }
      })(),
      language: el.getAttribute('language') || 'ru-UA',
      siteName: el.getAttribute('site-name') || '',
      siteDescription: el.getAttribute('site-description') || '',
      menuItem: (() => {
        const raw = el.getAttribute('menu-item')
        if (!raw) return null
        try {
          return JSON.parse(raw)
        } catch (e) {
          console.error('Invalid menu-item JSON for exussalebanner:', e)
          return null
        }
      })(),
              linkParams: (() => {
                const raw = el.getAttribute('link-params')
                if (!raw) return null
                try {
                  return JSON.parse(raw)
                } catch (e) {
                  console.error('Invalid link-params JSON for exussalebanner:', e)
                  return null
                }
              })(),
              banner: (() => {
                const raw = el.getAttribute('banner')
                if (!raw) return null
                try {
                  return JSON.parse(raw)
                } catch (e) {
                  console.error('Invalid banner JSON for exussalebanner:', e)
                  return null
                }
              })()
            }
    console.log('Mounting exussalebanner with props:', props)
    try {
      const app = createApp(Exussalebanner, props)
      app.mount(el)
      el.__vue_app__ = app
      console.log('Exussalebanner mounted successfully')
    } catch (e) {
      console.error('Error mounting exussalebanner:', e)
    }
  })
}

// mountExussalebannerIslands() is now called in the general mounting block

// Mount ExussalebannerList islands
function mountExussalebannerListIslands() {
  const nodes = document.querySelectorAll('exussalebanner-list-component')
  console.log('Mounting exussalebanner-list islands, found nodes:', nodes.length)
  nodes.forEach((el) => {
    if (el.__vue_app__) {
      console.log('ExussalebannerList already mounted, skipping')
      return
    }
    
            const props = {
              menuItems: (() => {
                const raw = el.getAttribute('menu-items')
                if (!raw) return []
                try {
                  return JSON.parse(raw)
                } catch (e) {
                  console.error('Invalid menu-items JSON for exussalebanner-list:', e)
                  return []
                }
              })(),
              language: el.getAttribute('language') || 'ru-UA',
              siteName: el.getAttribute('site-name') || '',
              siteDescription: el.getAttribute('site-description') || '',
              menuItem: (() => {
                const raw = el.getAttribute('menu-item')
                if (!raw) return null
                try {
                  return JSON.parse(raw)
                } catch (e) {
                  console.error('Invalid menu-item JSON for exussalebanner-list:', e)
                  return null
                }
              })(),
              linkParams: (() => {
                const raw = el.getAttribute('link-params')
                if (!raw) return null
                try {
                  return JSON.parse(raw)
                } catch (e) {
                  console.error('Invalid link-params JSON for exussalebanner-list:', e)
                  return null
                }
              })(),
              banners: (() => {
                const raw = el.getAttribute('banners')
                if (!raw) return []
                try {
                  return JSON.parse(raw)
                } catch (e) {
                  console.error('Invalid banners JSON for exussalebanner-list:', e)
                  return []
                }
              })(),
              pagination: (() => {
                const raw = el.getAttribute('pagination')
                if (!raw) return null
                try {
                  return JSON.parse(raw)
                } catch (e) {
                  console.error('Invalid pagination JSON for exussalebanner-list:', e)
                  return null
                }
              })()
            }
    console.log('Mounting exussalebanner-list with props:', props)
    try {
      const app = createApp(ExussalebannerList, props)
      app.mount(el)
      el.__vue_app__ = app
      console.log('ExussalebannerList mounted successfully')
    } catch (e) {
      console.error('Error mounting exussalebanner-list:', e)
    }
  })
}

// mountExussalebannerListIslands() is now called in the general mounting block

// Mount all islands
if (document.readyState === 'interactive' || document.readyState === 'complete') {
  mountVueApps()
  mountCartModalIslands()
  mountSiteMenuIslands()
  mountHomepageIslands()
  mountContentIslands()
  mountContentListIslands()
  mountExussalebannerIslands()
  mountExussalebannerListIslands()
} else {
  document.addEventListener('DOMContentLoaded', () => {
    mountVueApps()
    mountCartModalIslands()
    mountSiteMenuIslands()
    mountHomepageIslands()
    mountContentIslands()
    mountContentListIslands()
    mountExussalebannerIslands()
    mountExussalebannerListIslands()
  })
}