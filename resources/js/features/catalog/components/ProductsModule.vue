<template>
  <div class="random-products">
    <!-- SSR HTML mode -->
    <div v-if="html" v-html="html"></div>

    <!-- Skeleton -->
    <div v-else-if="isLoading" class="rp-skeleton">
      <div class="rp-card" v-for="i in 3" :key="i">
        <div class="sk-img" />
        <div class="sk-line w-80" />
        <div class="sk-line w-60" />
      </div>
    </div>

    <!-- CSR fallback -->
    <div v-else class="rp-grid">
      <a v-for="p in products" :key="p.id" :href="p.url || ('/products/' + p.id)" class="rp-card">
        <div class="rp-imgwrap">
          <img :src="p.image" :alt="p.name" class="rp-img" loading="lazy" />
        </div>
        <div class="rp-title" :title="p.name">{{ p.name }}</div>
      </a>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  type: { type: String, default: 'random' },
  limit: { type: Number, default: 3 },
  apiUrl: { type: String, default: '/api/products' }
})

const isLoading = ref(true)
const products = ref([])
const html = ref('')

function pickRandom(list, n) {
  const arr = [...list]
  for (let i = arr.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1))
    ;[arr[i], arr[j]] = [arr[j], arr[i]]
  }
  return arr.slice(0, n)
}

onMounted(async () => {
  try {
    // First try SSR HTML endpoint
    try {
      const htmlRes = await fetch(`/products/html?limit=${encodeURIComponent(props.limit)}${(props.type || 'random').toLowerCase()==='random' || (props.type||'').toLowerCase()==='rundom' ? '&random=1' : ''}`)
      if (htmlRes.ok) {
        html.value = await htmlRes.text()
        return
      }
    } catch (e) { /* ignore and fallback */ }

    const t = (props.type || 'random').toLowerCase()
    const isRandom = t === 'random' || t === 'rundom'
    const url = isRandom ? `${props.apiUrl}?limit=${encodeURIComponent(props.limit)}&random=1` : `${props.apiUrl}?limit=${encodeURIComponent(props.limit)}`
    let res = await fetch(url)
    if (!res.ok) {
      // fallback without params
      res = await fetch(props.apiUrl)
    }
    async function normalizeAndSet(resJson) {
      const data = resJson
      const candidates = [
        data.products,
        data.items,
        data.data,
        data.rows,
        Array.isArray(data) ? data : null
      ].find(a => Array.isArray(a)) || []
      const list = candidates
      const rnd = (data.random || isRandom) ? (data.random ? list : pickRandom(list, props.limit)) : list.slice(0, props.limit)
      products.value = rnd.map(x => ({
        id: x.id ?? x.product_id ?? Math.random().toString(36).slice(2),
        name: x.name ?? x.title ?? x.product_name ?? 'Товар',
        image: x.image ?? x.img ?? '/images/placeholder.png',
        url: x.url ?? (x.slug ? `/products/${x.slug}` : undefined)
      }))
    }
    if (res.ok) {
      const data = await res.json()
      // If no recognizable list, try explicit API endpoint as fallback
      const hasList = Array.isArray(data) || Array.isArray(data.products) || Array.isArray(data.items) || Array.isArray(data.data)
      if (!hasList && props.apiUrl !== '/api/products') {
        const r2 = await fetch(`/api/products?limit=${encodeURIComponent(props.limit)}${isRandom ? '&random=1' : ''}`)
        if (r2.ok) {
          const j2 = await r2.json()
          await normalizeAndSet(j2)
        }
      } else {
        await normalizeAndSet(data)
      }
    }
  } catch (e) {
    products.value = []
  } finally {
    isLoading.value = false
  }
})
</script>

<style>
.rp-grid { display: flex; gap: 32px; }
.rp-card { flex: 1;display: block; text-decoration: none; color: #111827; }
.rp-imgwrap { width: 100%; aspect-ratio: 1/1; display: flex; align-items: center; justify-content: center; background: #f8fafc; border-radius: 6px; }
.rp-img { max-width: 70%; max-height: 70%; object-fit: contain; filter: drop-shadow(0 1px 3px rgba(0,0,0,0.08)); }
.rp-title { margin-top: 16px; font-weight: 700; line-height: 1.4; }

/* skeleton with fixed height matching final layout */
.rp-skeleton { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
.rp-card { }
.sk-img { width: 100%; aspect-ratio: 1/1; border-radius: 6px; background: linear-gradient(90deg,#eee 25%,#f5f5f5 37%,#eee 63%); background-size: 400% 100%; animation: shimmer 1.4s ease infinite; }
.sk-line { height: 14px; margin-top: 10px; border-radius: 6px; background: linear-gradient(90deg,#eee 25%,#f5f5f5 37%,#eee 63%); background-size: 400% 100%; animation: shimmer 1.4s ease infinite; }
.sk-line.w-80 { width: 80%; }
.sk-line.w-60 { width: 60%; }
@keyframes shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
</style>


