<template>
  <button
    class="btn"
    :class="computedClass"
    v-bind="$attrs"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: { type: String, default: 'primary' }
})

const computedClass = computed(() => `btn-${props.variant}`)
</script>

<style scoped>
</style>


