<template>
  <div class="exussalebanner-component">
    <div class="container py-4">
      <div class="row">
        <div class="col-12">
          
          <div v-if="linkParams?.option === 'com_exussalebanner' && banner" class="banner-body">
            <!-- Banner Image -->
            <div v-if="banner.image" class="banner-image mb-4">
              <img :src="banner.image" :alt="banner.title" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
            </div>
            
            <!-- Banner Description -->
            <div v-if="banner.description" class="banner-description mb-4" v-html="banner.description"></div>
            
            <!-- Banner Full Text -->
            <div v-if="banner.fulltext" class="banner-fulltext mb-4" v-html="banner.fulltext"></div>
            
            <!-- Banner Period -->
            <div v-if="banner.sale_start || banner.sale_end" class="banner-period mb-4">
              <div class="alert alert-info">
                <h5>Период проведения акции:</h5>
                <p class="mb-0">
                  <span v-if="banner.sale_start">{{ new Date(banner.sale_start).toLocaleDateString() }}</span>
                  <span v-if="banner.sale_start && banner.sale_end"> - </span>
                  <span v-if="banner.sale_end">{{ new Date(banner.sale_end).toLocaleDateString() }}</span>
                </p>
              </div>
            </div>
            
            <!-- Banner Meta Info -->
            <div class="banner-meta text-muted">
              <small>
                <span v-if="banner.created">Создано: {{ new Date(banner.created).toLocaleDateString() }}</span>
                <span v-if="banner.id"> | ID: {{ banner.id }}</span>
              </small>
            </div>
          </div>
          
          <div v-else-if="linkParams?.option === 'com_exussalebanner'" class="alert alert-warning">
            <h5>Баннер не найден</h5>
            <p>Информация о баннере недоступна.</p>
          </div>
          
          <div v-else class="alert alert-warning">
            <h5>Неизвестный тип баннера</h5>
            <p>Параметры ссылки: {{ JSON.stringify(linkParams) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  menuItems: {
    type: Array,
    default: () => []
  },
  language: {
    type: String,
    default: 'uk'
  },
  siteName: {
    type: String,
    default: ''
  },
  siteDescription: {
    type: String,
    default: ''
  },
  menuItem: {
    type: Object,
    default: null
  },
  linkParams: {
    type: Object,
    default: null
  },
  banner: {
    type: Object,
    default: null
  }
})
</script>

<style scoped>
.exussalebanner-component {
  min-height: 400px;
}

.banner-body {
  background: transparent;
  padding: 0;
}

.alert {
  padding: 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 4px;
}

.alert-success {
  color: #155724;
  background-color: #d4edda;
  border-color: #c3e6cb;
}

.alert-warning {
  color: #856404;
  background-color: #fff3cd;
  border-color: #ffeaa7;
}
</style>



