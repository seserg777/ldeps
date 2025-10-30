<template>
  <nav v-if="pagination && pagination.last_page > 1" class="mt-4">
    <ul class="pagination justify-content-center">
      <!-- Previous button -->
      <li class="page-item" :class="{ disabled: pagination.current_page <= 1 }">
        <a 
          class="page-link" 
          :href="getPageUrl(pagination.current_page - 1)" 
          :aria-disabled="pagination.current_page <= 1"
          @click.prevent="goToPage(pagination.current_page - 1)"
        >
          {{ prevText }}
        </a>
      </li>
      
      <!-- Page numbers -->
      <li 
        v-for="page in getPageNumbers()" 
        :key="page" 
        class="page-item" 
        :class="{ active: page === pagination.current_page }"
      >
        <a 
          class="page-link" 
          :href="getPageUrl(page)" 
          @click.prevent="goToPage(page)"
        >
          {{ page }}
        </a>
      </li>
      
      <!-- Next button -->
      <li class="page-item" :class="{ disabled: pagination.current_page >= pagination.last_page }">
        <a 
          class="page-link" 
          :href="getPageUrl(pagination.current_page + 1)" 
          :aria-disabled="pagination.current_page >= pagination.last_page"
          @click.prevent="goToPage(pagination.current_page + 1)"
        >
          {{ nextText }}
        </a>
      </li>
    </ul>
    
    <!-- Pagination info -->
    <div v-if="showInfo" class="text-center text-muted mt-2">
      <small>
        {{ infoText }}
      </small>
    </div>
  </nav>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue'

const props = defineProps({
  pagination: {
    type: Object,
    default: null
  },
  prevText: {
    type: String,
    default: 'Предыдущая'
  },
  nextText: {
    type: String,
    default: 'Следующая'
  },
  showInfo: {
    type: Boolean,
    default: true
  },
  infoText: {
    type: String,
    default: ''
  },
  baseUrl: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['page-change'])

// Функция для генерации номеров страниц
const getPageNumbers = () => {
  if (!props.pagination) return []
  
  const current = props.pagination.current_page
  const last = props.pagination.last_page
  const pages = []
  
  // Показываем максимум 5 страниц
  let start = Math.max(1, current - 2)
  let end = Math.min(last, current + 2)
  
  // Корректируем если мы близко к началу или концу
  if (end - start < 4) {
    if (start === 1) {
      end = Math.min(last, start + 4)
    } else {
      start = Math.max(1, end - 4)
    }
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
}

// Генерация URL для страницы
const getPageUrl = (page) => {
  if (props.baseUrl) {
    return `${props.baseUrl}?page=${page}`
  }
  
  const url = new URL(window.location)
  url.searchParams.set('page', page)
  return url.toString()
}

// Переход на страницу
const goToPage = (page) => {
  if (page < 1 || page > props.pagination.last_page) return
  
  emit('page-change', page)
  
  // Если не обработано родительским компонентом, делаем переход
  window.location.href = getPageUrl(page)
}

// Генерация текста информации
const infoText = computed(() => {
  if (props.infoText) return props.infoText
  
  const { current_page, last_page, total } = props.pagination
  return `Показано ${total} элементов (страница ${current_page} из ${last_page})`
})
</script>

<style scoped>
.pagination {
  margin-bottom: 0;
}

.page-link {
  color: #007bff;
  text-decoration: none;
  border: 1px solid #dee2e6;
  padding: 0.5rem 0.75rem;
  margin-left: -1px;
  line-height: 1.25;
  background-color: #fff;
  transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
}

.page-link:hover {
  color: #0056b3;
  background-color: #e9ecef;
  border-color: #dee2e6;
}

.page-item.active .page-link {
  color: #fff;
  background-color: #007bff;
  border-color: #007bff;
}

.page-item.disabled .page-link {
  color: #6c757d;
  pointer-events: none;
  background-color: #fff;
  border-color: #dee2e6;
}
</style>
