<template>
  <div class="card product-card h-100 shadow-sm border-0">
    <ProductImage
      :src="product.thumbnail_url"
      :alt="product.name"
      :url="product.url"
      :product-id="product.product_id"
      :hits="product.hits || 0"
      :is-sale="product.product_price < 1000"
      @cart="handleAddToCart"
      @wishlist="handleAddToWishlist"
    />

    <div class="card-body d-flex flex-column p-3">
      <h6 class="card-title mb-2 fw-bold text-dark">
        <a :href="productUrl" class="text-decoration-none text-dark">
          {{ product.name }}
        </a>
      </h6>

      <p class="card-text text-muted small mb-2 flex-grow-1">
        {{ productDescription }}
      </p>

      <div v-if="product.extra_fields && product.extra_fields.length > 0" class="product-extra-fields mb-2">
        <div v-for="field in product.extra_fields" :key="field.field_name" class="extra-field-item mb-1">
          <span class="fw-bold text-dark small">{{ field.field_name }}:</span>
          <span class="text-muted small">{{ field.field_value }}</span>
        </div>
      </div>

      <div class="product-rating mb-2">
        <div class="stars d-inline-block">
          <i v-for="i in 5" :key="i" :class="i <= productRating ? 'fas fa-star text-warning' : 'far fa-star text-warning'" />
        </div>
        <small class="text-muted ms-1">({{ product.reviews_count || Math.floor(Math.random() * 50) + 10 }})</small>
      </div>

      <ProductPrice :price="product.product_price" :formatted-price="product.formatted_price" :old-price="product.old_price" />

      <div class="product-actions">
        <div class="d-flex justify-content-between align-items-center">
          <small class="text-muted">
            <i class="fas fa-eye me-1"></i>{{ product.hits || 0 }} views
          </small>
          <ProductActions :product-id="product.product_id" :url="productUrl" @cart="handleAddToCart" @wishlist="handleAddToWishlist" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ProductImage, ProductPrice, ProductActions } from './parts'

const props = defineProps({
  product: { type: Object, required: true }
})

const emit = defineEmits(['add-to-cart', 'add-to-wishlist'])

const productUrl = computed(() => props.product.url || `/products/${props.product.product_id}`)
const productRating = computed(() => props.product.rating || 4)
const productDescription = computed(() => 
  props.product.short_description || props.product.product_short_description || 
  'High quality optical cable for professional use'
)

const handleAddToCart = () => emit('add-to-cart', props.product.product_id)
const handleAddToWishlist = () => emit('add-to-wishlist', props.product.product_id)
</script>

<style scoped>
.product-card { transition: transform 0.3s ease, box-shadow 0.3s ease; height: 100%; }
.product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
.product-extra-fields { border-top: 1px solid #f0f0f0; padding-top: 8px; margin-top: 8px; }
.extra-field-item { display: flex; justify-content: space-between; align-items: center; padding: 2px 0; }
.extra-field-item span:first-child { flex: 0 0 auto; margin-right: 8px; }
.extra-field-item span:last-child { flex: 1; text-align: right; font-size: 0.8rem; }
@media (max-width: 576px) { .extra-field-item { flex-direction: column; align-items: flex-start; } .extra-field-item span:last-child { text-align: left; margin-top: 2px; } }
</style>


