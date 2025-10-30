<!--
  Homepage Component - Full Page Component
  
  This component is responsible for rendering the entire homepage structure:
  - Top menu navigation
  - Hero section
  - Featured categories
  - Featured products  
  - Sale banners
  - Footer
  
  This approach allows each page type to have its own complete component
  that handles the entire page layout and structure.
-->
<template>
  <div class="homepage">
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
        <div class="row justify-content-center">
          <div class="col-md-8 text-center py-5">
            <h1 class="display-4 fw-bold text-primary mb-4">
              Homepage Component
            </h1>
            <p class="lead text-muted">
              Компонент главной страницы
            </p>
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
import { computed } from 'vue'

const props = defineProps({
  // Menu data
  menuItems: {
    type: Array,
    default: () => []
  },
  // Language
  language: {
    type: String,
    default: 'ru-UA'
  },
  // Site information
  siteName: {
    type: String,
    default: 'Интернет-магазин'
  },
  siteDescription: {
    type: String,
    default: 'Лучшие товары по доступным ценам'
  }
})

// Computed properties
const currentYear = computed(() => new Date().getFullYear())
</script>

<style scoped>
.homepage {
  min-height: 100vh;
}

.hero-section {
  background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
}

.category-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.banner-card {
  transition: transform 0.3s ease;
}

.banner-card:hover {
  transform: translateY(-3px);
}

.object-fit-cover {
  object-fit: cover;
}
</style>
