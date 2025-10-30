<!--
  CatalogPage Component - Full Page Component for Product Catalog
  
  This component is responsible for rendering the entire catalog page structure:
  - Top menu navigation
  - Breadcrumbs
  - Filters sidebar
  - Product grid/list
  - Pagination
  - Footer
  
  This follows the same pattern as Homepage - each page type has its own
  complete component that handles the entire page layout.
-->
<template>
  <div class="catalog-page">
    <!-- Top Menu -->
    <div class="bg-light border-bottom">
      <div class="container py-2">
        <site-menu 
          menutype="main-menu-add" 
          layout="default" 
          :language="language"
          :items="menuItems"
        />
      </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
      <div class="container">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="py-3">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="/">Главная</a>
            </li>
            <li v-for="crumb in breadcrumbs" :key="crumb.url" class="breadcrumb-item">
              <a v-if="crumb.url" :href="crumb.url">{{ crumb.title }}</a>
              <span v-else>{{ crumb.title }}</span>
            </li>
          </ol>
        </nav>

        <!-- Page Header -->
        <div class="row mb-4">
          <div class="col">
            <h1 class="h2">{{ pageTitle }}</h1>
            <p v-if="pageDescription" class="text-muted">{{ pageDescription }}</p>
          </div>
        </div>

        <!-- Filters and Products -->
        <div class="row">
          <!-- Filters Sidebar -->
          <div class="col-lg-3">
            <div class="card">
              <div class="card-header">
                <h5 class="mb-0">Фильтры</h5>
              </div>
              <div class="card-body">
                <!-- Category filters -->
                <div v-if="categories.length" class="mb-4">
                  <h6>Категории</h6>
                  <div class="list-group list-group-flush">
                    <a 
                      v-for="category in categories" 
                      :key="category.id"
                      :href="category.url"
                      class="list-group-item list-group-item-action"
                    >
                      {{ category.name }}
                      <span class="badge bg-secondary ms-2">{{ category.count }}</span>
                    </a>
                  </div>
                </div>

                <!-- Price range -->
                <div class="mb-4">
                  <h6>Цена</h6>
                  <div class="row">
                    <div class="col-6">
                      <input 
                        type="number" 
                        class="form-control form-control-sm" 
                        placeholder="От"
                        v-model="filters.priceMin"
                      >
                    </div>
                    <div class="col-6">
                      <input 
                        type="number" 
                        class="form-control form-control-sm" 
                        placeholder="До"
                        v-model="filters.priceMax"
                      >
                    </div>
                  </div>
                </div>

                <!-- Apply filters button -->
                <button class="btn btn-primary w-100" @click="applyFilters">
                  Применить фильтры
                </button>
              </div>
            </div>
          </div>

          <!-- Products Grid -->
          <div class="col-lg-9">
            <!-- Sort and view options -->
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="text-muted">Найдено товаров: {{ totalProducts }}</span>
              </div>
              <div class="btn-group" role="group">
                <button 
                  class="btn btn-outline-secondary"
                  :class="{ active: viewMode === 'grid' }"
                  @click="viewMode = 'grid'"
                >
                  <i class="fas fa-th"></i>
                </button>
                <button 
                  class="btn btn-outline-secondary"
                  :class="{ active: viewMode === 'list' }"
                  @click="viewMode = 'list'"
                >
                  <i class="fas fa-list"></i>
                </button>
              </div>
            </div>

            <!-- Products -->
            <div :class="viewMode === 'grid' ? 'row' : ''">
              <div 
                v-for="product in products" 
                :key="product.id"
                :class="viewMode === 'grid' ? 'col-lg-4 col-md-6 mb-4' : 'mb-3'"
              >
                <ProductCard 
                  :product="product"
                  :view-mode="viewMode"
                  @add-to-cart="handleAddToCart"
                  @add-to-wishlist="handleAddToWishlist"
                />
              </div>
            </div>

            <!-- Pagination -->
            <nav v-if="pagination.total > pagination.perPage" aria-label="Page navigation">
              <ul class="pagination justify-content-center">
                <li class="page-item" :class="{ disabled: !pagination.prevPageUrl }">
                  <a class="page-link" :href="pagination.prevPageUrl">Предыдущая</a>
                </li>
                <li 
                  v-for="page in pagination.pages" 
                  :key="page"
                  class="page-item"
                  :class="{ active: page === pagination.currentPage }"
                >
                  <a class="page-link" :href="pagination.getUrlForPage(page)">{{ page }}</a>
                </li>
                <li class="page-item" :class="{ disabled: !pagination.nextPageUrl }">
                  <a class="page-link" :href="pagination.nextPageUrl">Следующая</a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <h5>{{ siteName }}</h5>
            <p class="text-muted">{{ siteDescription }}</p>
          </div>
          <div class="col-md-6 text-md-end">
            <p class="text-muted mb-0">&copy; {{ currentYear }} {{ siteName }}. Все права защищены.</p>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Product } from './Product.vue'

const props = defineProps({
  // Page data
  pageTitle: { type: String, default: 'Каталог товаров' },
  pageDescription: { type: String, default: '' },
  
  // Products data
  products: { type: Array, default: () => [] },
  totalProducts: { type: Number, default: 0 },
  
  // Categories for filters
  categories: { type: Array, default: () => [] },
  
  // Pagination
  pagination: { type: Object, default: () => ({}) },
  
  // Menu data
  menuItems: { type: Array, default: () => [] },
  language: { type: String, default: 'ru-UA' },
  
  // Site information
  siteName: { type: String, default: 'Интернет-магазин' },
  siteDescription: { type: String, default: 'Лучшие товары по доступным ценам' },
  
  // Breadcrumbs
  breadcrumbs: { type: Array, default: () => [] }
})

const emit = defineEmits(['add-to-cart', 'add-to-wishlist', 'filter-changed'])

// Local state
const viewMode = ref('grid')
const filters = ref({
  priceMin: null,
  priceMax: null,
  category: null
})

// Computed properties
const currentYear = computed(() => new Date().getFullYear())

// Methods
const handleAddToCart = (productId) => {
  emit('add-to-cart', productId)
}

const handleAddToWishlist = (productId) => {
  emit('add-to-wishlist', productId)
}

const applyFilters = () => {
  emit('filter-changed', filters.value)
}

// Register components
defineOptions({
  components: {
    ProductCard: Product
  }
})
</script>

<style scoped>
.catalog-page {
  min-height: 100vh;
}

.main-content {
  min-height: calc(100vh - 200px);
}

.breadcrumb {
  background: none;
  padding: 0;
}

.list-group-item {
  border: none;
  padding: 0.5rem 0;
}

.list-group-item:hover {
  background: #f8f9fa;
}

.pagination .page-link {
  color: #0d6efd;
}

.pagination .page-item.active .page-link {
  background-color: #0d6efd;
  border-color: #0d6efd;
}
</style>
