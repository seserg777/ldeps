<template>
  <div class="auth-button d-inline-block">
    <button class="nav-link text-light border-0 bg-transparent position-relative" :title="btnTitle" @click="open">
      <span v-if="!isAuthenticated" class="icon-wrapper" aria-hidden="true">
        <!-- user with lock -->
        <i class="fas fa-user-lock"></i>
      </span>
      <span v-else class="icon-wrapper" aria-hidden="true">
        <!-- user without lock -->
        <i class="fas fa-user"></i>
      </span>
    </button>

    <AuthModal
      v-model="isOpen"
      :is-authenticated="isAuthenticated"
      :username="username"
      :csrf-token="csrfToken"
      :login-url="loginUrl"
      :logout-url="logoutUrl"
      size="sm"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import AuthModal from './AuthModal.vue'

const props = defineProps({
  isAuthenticated: { type: Boolean, default: false },
  username: { type: String, default: '' },
  loginUrl: { type: String, required: true },
  logoutUrl: { type: String, required: true },
  csrfToken: { type: String, required: true }
})

const isOpen = ref(false)
const btnTitle = props.isAuthenticated ? 'Обліковий запис' : 'Увійти'

function open() { isOpen.value = true }
</script>

<style scoped>
.icon-wrapper { display: inline-flex; align-items: center; justify-content: center; }
.icon-wrapper i { font-size: 20px; }
</style>


