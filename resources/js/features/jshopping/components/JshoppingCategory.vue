<template>
  <section class="jshop-category">
    <div v-if="isLoading" class="jshop-skel">
      <div v-for="i in 5" :key="i" class="col">
        <div class="sk-title" />
        <div class="sk-line" v-for="j in 4" :key="j" />
      </div>
    </div>
    <div v-else>
      <div v-if="!renderAsGrid" class="jshop-grid">
        <div v-for="col in children" :key="col.id" class="col">
          <a :href="col.url" class="heading">{{ col.name }}</a>
          <!-- Complexes under this top-level category -->
          <ul v-if="col.complexes && col.complexes.length" class="complexes">
            <li v-for="cx in col.complexes" :key="cx.id">
              <a :href="cx.url" class="levelc">{{ cx.name }}</a>
            </li>
          </ul>
          <!-- Second-level categories -->
          <ul v-if="col.children && col.children.length" class="list">
            <li v-for="sc in (col.children || [])" :key="sc.id">
              <a :href="sc.url" class="level2">{{ sc.name }}</a>
              <ul v-if="sc.children && sc.children.length" class="sublist">
                <li v-for="gg in sc.children" :key="gg.id">
                  <a :href="gg.url" class="level3">{{ gg.name }}</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
      <div v-else>
        <div v-if="isProductsLoading" class="prod-skel">
          <div v-for="i in 25" :key="i" class="ph" />
        </div>
        <div v-else class="prod-grid">
          <a v-for="p in products" :key="p.id" :href="p.url" class="prod-card">
            <div class="imgwrap"><img :src="p.thumbnail_url" :alt="p.name" /></div>
            <div class="ptitle">{{ p.name }}</div>
            <div class="pprice">{{ p.formatted_price }}</div>
          </a>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const props = defineProps({
  categoryId: { type: [Number, String], default: null },
  linkParams: { type: Object, default: null }
})

const isLoading = ref(true)
const category = ref(null)
const children = ref([])
const products = ref([])
const isProductsLoading = ref(false)

const id = computed(() => {
  if (props.categoryId) return Number(props.categoryId)
  return Number(props.linkParams?.category_id || 0)
})

const isRoot = computed(() => (category.value?.category_parent_id ?? 1) === 0)
const renderAsGrid = computed(() => !isRoot.value || (Array.isArray(children.value) && children.value.length === 0))

onMounted(async () => {
  try {
    if (!id.value) return
    const res = await fetch(`/api/jshopping/category/${encodeURIComponent(id.value)}`)
    if (res.ok) {
      const data = await res.json()
      category.value = data.category
      const baseChildren = Array.isArray(data.children) ? data.children : []
      // If nested children are missing, fetch them individually
      const enriched = await Promise.all(baseChildren.map(async (c) => {
        if (Array.isArray(c.children) && c.children.length) return c
        try {
          const r = await fetch(`/api/jshopping/category/${encodeURIComponent(c.id)}`)
          if (r.ok) {
            const j = await r.json()
            return { ...c, children: j.children || [] }
          }
        } catch (e) { /* ignore */ }
        return { ...c, children: c.children || [] }
      }))
      children.value = enriched
      // Load product grid for non-root categories
      if (!isRoot.value) {
        isProductsLoading.value = true
        try {
          const pr = await fetch(`/api/products?category=${encodeURIComponent(id.value)}&per_page=25`)
          if (pr.ok) {
            const pj = await pr.json()
            // Try multiple common shapes
            let list = []
            if (Array.isArray(pj.categories) && pj.categories.length && Array.isArray(pj.categories[0].products)) {
              list = pj.categories[0].products
            } else if (Array.isArray(pj.products?.data)) {
              list = pj.products.data
            } else if (Array.isArray(pj.products)) {
              list = pj.products
            } else if (Array.isArray(pj.data)) {
              list = pj.data
            }
            products.value = list.map(p => ({
              id: p.product_id ?? p.id,
              name: p.name ?? p.title,
              thumbnail_url: p.thumbnail_url ?? p.image ?? p.img,
              formatted_price: p.formatted_price ?? p.product_price ?? '',
              url: p.url ?? (p.full_path ? '/' + p.full_path : (p.slug ? '/products/' + p.slug : '#'))
            }))
          }
        } finally {
          isProductsLoading.value = false
        }
      }
    }
  } finally {
    isLoading.value = false
  }
})
</script>

<style scoped>
.jshop-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap: 24px; }
.col { background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; }
.heading { display: block; font-weight: 700; font-size: 18px; color: #111827; text-decoration: none; margin-bottom: 12px; }
.heading:hover { color: #0d6efd; }
.list { list-style: none; margin: 0; padding: 0; }
.list a { display: inline-block; padding: 6px 0; color: #374151; text-decoration: none; }
.list a:hover { color: #0d6efd; }
.complexes { margin: 6px 0 8px 0; padding-left: 14px; }
.complexes .levelc { font-size: 0.95rem; color: #0d6efd; }
.complexes .levelc:hover { text-decoration: underline; }

.jshop-skel { display: grid; grid-template-columns: repeat(5,1fr); gap: 24px; }
.sk-title { height: 18px; width: 60%; border-radius: 6px; background: linear-gradient(90deg,#eee 25%,#f5f5f5 37%,#eee 63%); background-size: 400% 100%; animation: shimmer 1.4s ease infinite; margin-bottom: 12px; }
.sk-line { height: 12px; width: 80%; border-radius: 6px; background: linear-gradient(90deg,#eee 25%,#f5f5f5 37%,#eee 63%); background-size: 400% 100%; animation: shimmer 1.4s ease infinite; margin: 8px 0; }
@keyframes shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }

.prod-grid { display: grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap: 16px; }
.prod-card { display: block; text-decoration: none; color: #111827; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; background:#fff; }
.imgwrap { width: 100%; aspect-ratio: 1/1; display:flex; align-items:center; justify-content:center; background:#f8fafc; border-radius:6px; }
.imgwrap img { max-width: 80%; max-height: 80%; object-fit: contain; }
.ptitle { margin-top: 10px; font-weight: 600; line-height: 1.3; min-height: 2.6em; }
.pprice { margin-top: 6px; color: #0d6efd; font-weight: 700; }
.prod-skel { display:grid; grid-template-columns: repeat(5, minmax(0,1fr)); gap:16px; }
.prod-skel .ph { height: 260px; border-radius: 8px; background: linear-gradient(90deg,#eee 25%,#f5f5f5 37%,#eee 63%); background-size: 400% 100%; animation: shimmer 1.4s ease infinite; }
</style>


