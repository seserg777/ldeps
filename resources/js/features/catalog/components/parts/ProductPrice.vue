<template>
  <div class="product-price mb-3">
    <div class="d-flex align-items-center">
      <span class="h5 mb-0 text-primary fw-bold">{{ formatted }}</span>
      <small v-if="oldPrice" class="text-muted text-decoration-line-through ms-2">{{ oldPrice }} ₴</small>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  price: { type: Number, required: true },
  formattedPrice: { type: String, default: '' },
  oldPrice: { type: [String, Number], default: null }
})

const formatted = computed(() => props.formattedPrice || new Intl.NumberFormat('uk-UA', { style: 'currency', currency: 'UAH' }).format(props.price))
</script>


