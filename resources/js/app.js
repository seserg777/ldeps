// Global styles
import '../css/app.css'
import Alpine from 'alpinejs'

window.Alpine = Alpine
Alpine.start()

// Cart actions
window.addToCart = function(productId) {
    const button = document.querySelector(`[data-product-id="${productId}"]`)
    if (!button) return

    const originalHtml = button.innerHTML
    button.disabled = true
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'

    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="fas fa-check"></i>'
            button.classList.remove('btn-primary')
            button.classList.add('btn-success')
            updateCartCount()
            setTimeout(() => {
                button.innerHTML = originalHtml
                button.classList.remove('btn-success')
                button.classList.add('btn-primary')
                button.disabled = false
            }, 2000)
        } else {
            button.innerHTML = originalHtml
            button.disabled = false
            console.error('Error adding to cart:', data.message)
        }
    })
    .catch(err => {
        button.innerHTML = originalHtml
        button.disabled = false
        console.error('Error:', err)
    })
}

window.addToWishlist = function(productId) {
    const button = document.querySelector(`[data-product-id="${productId}"]`)
    if (!button) return

    const originalHtml = button.innerHTML
    button.disabled = true
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'

    fetch('/wishlist/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="fas fa-check"></i>'
            button.classList.remove('btn-outline-danger')
            button.classList.add('btn-success')
            updateWishlistCount()
            setTimeout(() => {
                button.innerHTML = originalHtml
                button.classList.remove('btn-success')
                button.classList.add('btn-outline-danger')
                button.disabled = false
            }, 2000)
        } else {
            button.innerHTML = originalHtml
            button.disabled = false
            console.error('Error adding to wishlist:', data.message)
        }
    })
    .catch(err => {
        button.innerHTML = originalHtml
        button.disabled = false
        console.error('Error:', err)
    })
}

function updateCartCount() {
    fetch('/cart/count')
      .then(r => r.json())
      .then(data => {
        const el = document.getElementById('cart-count')
        if (el && data.count !== undefined) el.textContent = data.count
      })
      .catch(err => console.error('Error updating cart count:', err))
}

function updateWishlistCount() {
    fetch('/wishlist/count')
      .then(r => r.json())
      .then(data => {
        const el = document.getElementById('wishlist-count')
        if (el && data.count !== undefined) el.textContent = data.count
      })
      .catch(err => console.error('Error updating wishlist count:', err))
}

// Delegate clicks
document.addEventListener('click', (e) => {
  const btn = e.target && e.target.closest && e.target.closest('.add-to-cart')
  if (btn) {
    e.preventDefault()
    const productId = btn.getAttribute('data-product-id')
    if (productId) window.addToCart(productId)
  }
})