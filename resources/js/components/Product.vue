<template>
  <div class="card product-card h-100 shadow-sm border-0">
    <!-- Product Image Container -->
    <div class="position-relative overflow-hidden">
      <img 
        :src="product.thumbnail_url" 
        class="card-img-top product-image lazy" 
        :alt="product.name"
        loading="lazy"
        width="300"
        height="200"
        decoding="async"
      >
      
      <!-- Product Badges -->
      <div class="product-badges">
        <span 
          v-if="product.hits > 100" 
          class="badge bg-warning text-dark position-absolute top-0 start-0 m-2"
        >
          <i class="fas fa-fire me-1"></i>Хіт
        </span>
        <span 
          v-if="product.product_price < 1000" 
          class="badge bg-success position-absolute top-0 end-0 m-2"
        >
          <i class="fas fa-percent me-1"></i>Акція
        </span>
      </div>
      
      <!-- Quick Actions Overlay -->
      <div class="product-overlay">
        <div class="overlay-actions">
          <a 
            :href="product.url" 
            class="btn btn-light btn-sm rounded-circle me-2" 
            title="Швидкий перегляд"
          >
            <i class="fas fa-eye"></i>
          </a>
          <button 
            class="btn btn-danger btn-sm rounded-circle me-2 add-to-wishlist" 
            :data-product-id="product.product_id"
            title="Додати до бажань"
            @click="handleAddToWishlist"
          >
            <i class="fas fa-heart"></i>
          </button>
          <button 
            class="btn btn-primary btn-sm rounded-circle add-to-cart" 
            :data-product-id="product.product_id"
            title="Додати до кошика"
            @click="handleAddToCart"
          >
            <i class="fas fa-shopping-cart"></i>
          </button>
        </div>
      </div>
    </div>
    
    <!-- Product Info -->
    <div class="card-body d-flex flex-column p-3">
      <!-- Product Title -->
      <h6 class="card-title mb-2 fw-bold text-dark">
        <a 
          :href="product.url" 
          class="text-decoration-none text-dark"
        >
          {{ product.name }}
        </a>
      </h6>
      
      <!-- Product Description -->
      <p class="card-text text-muted small mb-2 flex-grow-1">
        {{ product.short_description || 'Оптичний кабель високої якості для професійного використання' }}
      </p>
      
      <!-- Product Extra Fields -->
      <div v-if="product.extra_fields && product.extra_fields.length > 0" class="product-extra-fields mb-2">
        <div 
          v-for="field in product.extra_fields" 
          :key="field.field_name"
          class="extra-field-item mb-1"
        >
          <span class="fw-bold text-dark small">{{ field.field_name }}:</span>
          <span class="text-muted small">{{ field.field_value }}</span>
        </div>
      </div>
      
      <!-- Rating -->
      <div class="product-rating mb-2">
        <div class="stars d-inline-block">
          <i 
            v-for="i in 5" 
            :key="i"
            :class="i <= product.rating ? 'fas fa-star text-warning' : 'far fa-star text-warning'"
          ></i>
        </div>
        <small class="text-muted ms-1">({{ product.reviews_count || Math.floor(Math.random() * 50) + 10 }})</small>
      </div>
      
      <!-- Price -->
      <div class="product-price mb-3">
        <div class="d-flex align-items-center">
          <span class="h5 mb-0 text-primary fw-bold">{{ product.formatted_price }}</span>
          <small 
            v-if="product.old_price" 
            class="text-muted text-decoration-line-through ms-2"
          >
            {{ product.old_price }} ₴
          </small>
        </div>
      </div>
      
      <!-- Product Actions -->
      <div class="product-actions">
        <div class="d-flex justify-content-between align-items-center">
          <small class="text-muted">
            <i class="fas fa-eye me-1"></i>{{ product.hits || 0 }} переглядів
          </small>
          <div class="btn-group" role="group">
            <a 
              :href="product.url" 
              class="btn btn-outline-primary btn-sm"
            >
              <i class="fas fa-info-circle me-1"></i>Деталі
            </a>
            <button 
              class="btn btn-outline-danger btn-sm add-to-wishlist" 
              :data-product-id="product.product_id"
              @click="handleAddToWishlist"
            >
              <i class="fas fa-heart me-1"></i>Бажання
            </button>
            <button 
              class="btn btn-primary btn-sm add-to-cart" 
              :data-product-id="product.product_id"
              @click="handleAddToCart"
            >
              <i class="fas fa-cart-plus me-1"></i>В кошик
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Props
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

// Emits
const emit = defineEmits(['add-to-cart', 'add-to-wishlist'])

// Computed
const product = computed(() => ({
  ...props.product,
  url: props.product.url || `/products/${props.product.product_id}`,
  rating: props.product.rating || 4,
  short_description: props.product.short_description || props.product.product_short_description
}))

// Methods
const handleAddToCart = () => {
  emit('add-to-cart', props.product.product_id)
}

const handleAddToWishlist = () => {
  emit('add-to-wishlist', props.product.product_id)
}
</script>

<style scoped>
.product-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  height: 100%;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.product-image {
  height: 200px;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.product-card:hover .product-image {
  transform: scale(1.05);
}

.product-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
  opacity: 1;
}

.overlay-actions {
  display: flex;
  gap: 0.5rem;
}

.product-extra-fields {
  border-top: 1px solid #f0f0f0;
  padding-top: 8px;
  margin-top: 8px;
}

.extra-field-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2px 0;
}

.extra-field-item span:first-child {
  flex: 0 0 auto;
  margin-right: 8px;
}

.extra-field-item span:last-child {
  flex: 1;
  text-align: right;
  font-size: 0.8rem;
}

@media (max-width: 576px) {
  .extra-field-item {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .extra-field-item span:last-child {
    text-align: left;
    margin-top: 2px;
  }
}
</style>
