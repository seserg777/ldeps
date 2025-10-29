require('./bootstrap');

// Vue components temporarily disabled for Tailwind compilation
// import { createApp } from 'vue'
// import CartModal from './components/CartModal.vue'

// // Create Vue app
// const app = createApp({
//   components: {
//     CartModal
//   }
// })

// // Mount the app
// app.mount('#app')

// Global cart modal functions
window.openCartModal = function() {
  if (window.cartModalInstance) {
    window.cartModalInstance.open()
  }
}

window.closeCartModal = function() {
  if (window.cartModalInstance) {
    window.cartModalInstance.close()
  }
}