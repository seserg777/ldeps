<template>
  <ui-modal v-model="isOpen" :title="modalTitle" :size="size">
    <div v-if="!isAuthenticated">
      <form :action="loginUrl" method="POST" @submit="onSubmit">
        <input type="hidden" name="_token" :value="csrfToken" />
        <input type="hidden" name="redirect" :value="redirectUrl" />
        <div class="mb-3">
          <label class="form-label">Login</label>
          <input v-model="username" name="username" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input v-model="password" name="password" type="password" class="form-control" required />
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Login</button>
        </div>
      </form>
    </div>

    <div v-else class="text-center">
      <p class="mb-3">Logged in as <strong>{{ usernameLabel }}</strong></p>
      <form :action="logoutUrl" method="POST">
        <input type="hidden" name="_token" :value="csrfToken" />
        <input type="hidden" name="redirect" :value="redirectUrl" />
        <button type="submit" class="btn btn-outline-danger">Logout</button>
      </form>
    </div>
  </ui-modal>
</template>

<script setup>
import UiModal from '@/shared/components/Ui/UiModal.vue'
import { computed, ref, watch } from 'vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  isAuthenticated: { type: Boolean, default: false },
  username: { type: String, default: '' },
  csrfToken: { type: String, required: true },
  loginUrl: { type: String, required: true },
  logoutUrl: { type: String, required: true },
  size: { type: String, default: 'sm' }
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
watch(() => props.modelValue, v => isOpen.value = v, { immediate: true })
watch(isOpen, v => emit('update:modelValue', v))

const usernameField = ref('')
const passwordField = ref('')

const usernameLabel = computed(() => props.username || 'user')
const modalTitle = computed(() => props.isAuthenticated ? 'Account' : 'Login')

const redirectUrl = computed(() => window.location.href)

const username = usernameField
const password = passwordField

function onSubmit() {
  // let the form submit normally
}
</script>


