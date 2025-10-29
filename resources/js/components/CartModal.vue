<template>
  <BaseModal
    id="cartModal"
    :title="`Кошик (${cartCount})`"
    size="md"
    :closable="true"
    :backdrop="true"
    :keyboard="true"
    :dynamic-title="true"
    v-model="isOpen"
    @open="loadCart"
  >
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
      <p class="text-gray-500 mt-2">Завантаження...</p>
    </div>

    <!-- Empty Cart -->
    <div v-else-if="cartItems.length === 0" class="text-center py-8">
      <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-4"></i>
      <p class="text-gray-500">Кошик порожній</p>
      <p class="text-sm text-gray-400">Додайте товари до кошика</p>
    </div>

    <!-- Cart Items -->
    <div v-else class="space-y-4 max-h-96 overflow-y-auto">
      <div
        v-for="item in cartItems"
        :key="item.product_id"
        class="flex items-center space-x-4 py-3 border-b border-gray-100 last:border-b-0"
      >
        <!-- Product Image -->
        <div class="flex-shrink-0">
          <img
            :src="item.image || '/images/no-image.svg'"
            :alt="item.name"
            class="w-16 h-16 object-cover rounded-lg"
            @error="$event.target.src = '/images/no-image.svg'"
          >
        </div>

        <!-- Product Info -->
        <div class="flex-1 min-w-0">
          <h4 class="text-sm font-medium text-gray-900 truncate">{{ item.name }}</h4>
          <p class="text-sm text-gray-500">Кількість: {{ item.quantity }}</p>
          <p class="text-sm font-semibold text-blue-600">{{ formatPrice(item.price) }}</p>
        </div>

        <!-- Remove Button -->
        <button
          @click="removeItem(item.product_id)"
          class="text-red-400 hover:text-red-600 p-1"
          title="Видалити з кошика"
        >
          <i class="fas fa-trash text-sm"></i>
        </button>
      </div>
    </div>

    <!-- Footer -->
    <template #footer>
      <div v-if="!loading && cartItems.length > 0" class="w-full">
        <div class="flex items-center justify-between mb-4">
          <span class="text-lg font-semibold">Всього:</span>
          <span class="text-lg font-bold text-blue-600">{{ formatPrice(totalPrice) }}</span>
        </div>
        <div class="flex justify-end">
          <a
            :href="cartIndexUrl"
            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-center"
          >
            Перейти до кошика
          </a>
        </div>
      </div>
    </template>
  </BaseModal>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import BaseModal from './BaseModal.vue'

const props = defineProps({
  cartIndexUrl: {
    type: String,
    required: true
  },
  cartModalUrl: {
    type: String,
    required: true
  },
  cartRemoveUrl: {
    type: String,
    required: true
  },
  csrfToken: {
    type: String,
    required: true
  }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const loading = ref(false)
const cartItems = ref([])
const cartCount = ref(0)
const totalPrice = ref(0)

const loadCart = async () => {
  loading.value = true
  try {
    const response = await fetch(props.cartModalUrl)
    const data = await response.json()

    cartItems.value = data.items || []
    cartCount.value = data.count || 0
    totalPrice.value = data.total || 0
  } catch (error) {
    console.error('Error loading cart:', error)
  } finally {
    loading.value = false
  }
}

const removeItem = async (productId) => {
  try {
    const response = await fetch(props.cartRemoveUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': props.csrfToken
      },
      body: JSON.stringify({ product_id: productId })
    })

    if (response.ok) {
      await loadCart()
      updateHeaderCartCount()
    }
  } catch (error) {
    console.error('Error removing item:', error)
  }
}

const updateHeaderCartCount = () => {
  const cartCountElement = document.getElementById('cart-count')
  if (cartCountElement) {
    cartCountElement.textContent = cartCount.value
  }
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('uk-UA', {
    style: 'currency',
    currency: 'UAH'
  }).format(price)
}

const open = () => {
  isOpen.value = true
}

const close = () => {
  isOpen.value = false
}

// Expose methods for parent components
defineExpose({
  open,
  close
})

onMounted(() => {
  // Make cart modal globally accessible
  window.cartModalInstance = {
    open,
    close
  }
})
</script>
