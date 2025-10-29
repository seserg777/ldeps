<template>
  <div v-if="modelValue" class="modal fade show d-block" tabindex="-1" role="dialog" @click.self="close">
    <div class="modal-dialog" :class="dialogClass" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <slot name="title">{{ title }}</slot>
          </h5>
          <button type="button" class="btn-close" aria-label="Close" @click="close"></button>
        </div>
        <div class="modal-body">
          <slot />
        </div>
        <div v-if="$slots.footer" class="modal-footer">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: '' },
  size: { type: String, default: '' } // '', 'sm', 'lg', 'xl'
})

const emit = defineEmits(['update:modelValue'])

const dialogClass = computed(() => {
  return props.size ? `modal-${props.size}` : ''
})

const close = () => emit('update:modelValue', false)

const toggleBodyScroll = (enable) => {
  document.body.classList.toggle('modal-open', enable)
  document.body.style.overflow = enable ? 'hidden' : ''
}

watch(() => props.modelValue, (v) => toggleBodyScroll(v), { immediate: true })
onBeforeUnmount(() => toggleBodyScroll(false))
</script>

<style scoped>
.modal { background: rgba(0,0,0,.5); }
</style>


