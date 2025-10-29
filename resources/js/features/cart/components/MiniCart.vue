<template>
  <div class="minicart d-inline-block">
    <button class="nav-link text-light border-0 bg-transparent position-relative" :title="title" @click="open">
      <i class="fas fa-shopping-cart"></i>
      <span class="badge bg-danger ms-1" :class="{ 'position-absolute top-0 start-100 translate-middle': useFloat }">
        {{ count }}
      </span>
    </button>

    <MiniCartModal
      v-model="isOpen"
      :cart-index-url="cartIndexUrl"
      :cart-modal-url="cartModalUrl"
      :cart-remove-url="cartRemoveUrl"
      :csrf-token="csrfToken"
      @updated="updateCount"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import MiniCartModal from './MiniCartModal.vue'

const props = defineProps({
  cartIndexUrl: { type: String, required: true },
  cartModalUrl: { type: String, required: true },
  cartRemoveUrl: { type: String, required: true },
  csrfToken: { type: String, required: true },
  countUrl: { type: String, default: '/cart/count' },
  initialCount: { type: Number, default: 0 },
  title: { type: String, default: 'Cart' },
  useFloat: { type: Boolean, default: false }
})

const isOpen = ref(false)
const count = ref(props.initialCount)

function open() { isOpen.value = true }
function updateCount(newCount) { count.value = newCount }

async function fetchCount() {
  try {
    const res = await fetch(props.countUrl)
    const data = await res.json()
    if (typeof data.count !== 'undefined') count.value = data.count
  } catch (e) { /* ignore */ }
}

onMounted(fetchCount)
</script>

<style scoped>
.minicart .badge { min-width: 22px; }
</style>


