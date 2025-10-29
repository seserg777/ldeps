<template>
  <UiModal v-model="isOpen" :title="`Кошик (${cartCount})`" size="lg">
    <div v-if="loading" class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Завантаження...</span>
      </div>
    </div>

    <div v-else>
      <div v-if="items.length === 0" class="text-center text-muted py-4">
        <p class="mb-2">Кошик порожній</p>
        <p class="small">Додайте товари до кошика</p>
      </div>
      <div v-else>
        <ul class="list-group mb-3">
          <li v-for="item in items" :key="item.product_id" class="list-group-item d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
              <img :src="item.image" alt="" class="me-3" width="48" height="48" style="object-fit:cover" />
              <div>
                <div class="fw-semibold">{{ item.name }}</div>
                <small class="text-muted">x{{ item.quantity }}</small>
              </div>
            </div>
            <div class="text-end">
              <div class="fw-semibold">{{ item.formatted_price }}</div>
              <button class="btn btn-link text-danger p-0 small" @click="remove(item.product_id)">Видалити</button>
            </div>
          </li>
        </ul>
        <div class="d-flex justify-content-between align-items-center">
          <div class="fw-bold">Всього: {{ total }}</div>
          <a :href="cartIndexUrl" class="btn btn-primary">Перейти до кошика</a>
        </div>
      </div>
    </div>
  </UiModal>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import UiModal from '@/shared/components/Ui/UiModal.vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  cartIndexUrl: { type: String, required: true },
  cartModalUrl: { type: String, required: true },
  cartRemoveUrl: { type: String, required: true },
  csrfToken: { type: String, required: true }
})

const emit = defineEmits(['update:modelValue', 'updated'])

const isOpen = ref(false)
const loading = ref(false)
const items = ref([])
const cartCount = computed(() => items.value.reduce((a, i) => a + Number(i.quantity || 0), 0))
const total = computed(() => {
  const sum = items.value.reduce((a, i) => a + Number(i.total || 0), 0)
  return new Intl.NumberFormat('uk-UA', { style: 'currency', currency: 'UAH' }).format(sum)
})

watch(() => props.modelValue, (v) => { isOpen.value = v; if (v) load() }, { immediate: true })
watch(isOpen, (v) => emit('update:modelValue', v))

async function load() {
  loading.value = true
  try {
    const res = await fetch(props.cartModalUrl)
    const data = await res.json().catch(() => null)
    if (data && data.items) {
      items.value = data.items
    } else {
      // Fallback: try to fetch HTML and parse minimal data
      const html = await res.text()
      items.value = []
    }
    emit('updated', cartCount.value)
  } catch (e) {
    console.error('MiniCart load error', e)
  } finally {
    loading.value = false
  }
}

async function remove(productId) {
  try {
    const response = await fetch(props.cartRemoveUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': props.csrfToken },
      body: JSON.stringify({ product_id: productId })
    })
    if (response.ok) {
      await load()
    }
  } catch (e) { console.error('MiniCart remove error', e) }
}
</script>


