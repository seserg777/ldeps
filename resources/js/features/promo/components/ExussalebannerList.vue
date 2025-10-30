<template>
  <div class="exussalebanner-list-component">
    <div class="container py-4">
      <div class="row">
        <div class="col-12">
          <div v-if="linkParams?.option === 'com_exussalebanner' && linkParams?.view === 'exussalebanner'" class="banner-list">
            <!-- Banners list -->
            <div class="row">
              <div v-for="banner in banners" :key="banner.id" class="col-md-4 col-lg-3 mb-4">
                <div class="card banner-preview">
                  <div v-if="banner.image" class="card-img-top">
                    <img :src="banner.image" :alt="banner.title" class="img-fluid" style="height: 200px; object-fit: cover;">
                  </div>
                  <div class="card-body">
                    <h5 class="card-title">{{ banner.title }}</h5>
                    <div v-if="banner.description" class="card-text" v-html="banner.description"></div>
                    <div v-if="banner.sale_start || banner.sale_end" class="mt-2">
                      <small class="text-muted">
                        <strong>Период акции:</strong><br>
                        <span v-if="banner.sale_start">{{ new Date(banner.sale_start).toLocaleDateString() }}</span>
                        <span v-if="banner.sale_start && banner.sale_end"> - </span>
                        <span v-if="banner.sale_end">{{ new Date(banner.sale_end).toLocaleDateString() }}</span>
                      </small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <a :href="`/${menuItem?.path || 'promo'}/${banner.id}.html`" class="btn btn-primary btn-sm">
                        Подробнее
                      </a>
                      <small class="text-muted" v-if="banner.created">
                        {{ new Date(banner.created).toLocaleDateString() }}
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-if="banners.length === 0" class="alert alert-info">
              <p>Баннеры не найдены.</p>
            </div>
            
            <!-- Pagination -->
            <Pagination 
              :pagination="pagination"
              :info-text="`Показано ${banners.length} из ${pagination?.total || 0} баннеров`"
            />
          </div>
          
          <div v-else class="alert alert-warning">
            <h5>Неверные параметры для листинга баннеров</h5>
            <p>Ожидается: option=com_exussalebanner&view=exussalebanner</p>
            <p>Получено: {{ JSON.stringify(linkParams) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
import { Pagination } from '../../common/components'

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
  banners: {
    type: Array,
    default: () => []
  },
  pagination: {
    type: Object,
    default: null
  }
})

// Use banners from props
console.log('ExussalebannerList mounted with banners:', props.banners)
</script>

<style scoped>
.exussalebanner-list-component {
  min-height: 400px;
}

.banner-list {
  /* Remove unnecessary border */
}

.banner-preview {
  height: 100%;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.banner-preview:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.alert {
  padding: 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 4px;
}

.alert-info {
  color: #0c5460;
  background-color: #d1ecf1;
  border-color: #bee5eb;
}

.alert-warning {
  color: #856404;
  background-color: #fff3cd;
  border-color: #ffeaa7;
}
</style>
