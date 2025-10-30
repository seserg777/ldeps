<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape="close"
        @click.self="close"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50"></div>

        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 transform scale-95"
            enter-to-class="opacity-100 transform scale-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 transform scale-100"
            leave-to-class="opacity-0 transform scale-95"
          >
            <div
              v-if="isOpen"
              :class="modalSizeClass"
              class="relative transform overflow-hidden rounded-lg bg-white shadow-xl"
            >
              <!-- Header -->
              <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">
                  <span v-if="dynamicTitle" v-text="title"></span>
                  <span v-else>{{ title }}</span>
                </h3>
                <button
                  v-if="closable"
                  @click="close"
                  class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full p-1"
                >
                  <i class="fas fa-times text-xl"></i>
                </button>
              </div>

              <!-- Body -->
              <div class="px-6 py-4">
                <slot></slot>
              </div>

              <!-- Footer -->
              <div v-if="$slots.footer" class="border-t border-gray-200 px-6 py-4">
                <slot name="footer"></slot>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
  id: { type: String, default: 'modal' },
  title: { type: String, default: 'Modal Title' },
  size: { type: String, default: 'md', validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value) },
  closable: { type: Boolean, default: true },
  backdrop: { type: Boolean, default: true },
  keyboard: { type: Boolean, default: true },
  dynamicTitle: { type: Boolean, default: false },
  modelValue: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue', 'close', 'open'])

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const modalSizeClass = computed(() => {
  const sizes = { sm: 'max-w-sm', md: 'max-w-md', lg: 'max-w-lg', xl: 'max-w-xl' }
  return `w-full ${sizes[props.size]}`
})

const close = () => {
  isOpen.value = false
  emit('close')
}

const open = () => {
  isOpen.value = true
  emit('open')
}

watch(isOpen, (newValue) => {
  if (newValue) {
    document.body.style.overflow = 'hidden'
    emit('open')
  } else {
    document.body.style.overflow = ''
    emit('close')
  }
})

defineExpose({ open, close })
</script>

