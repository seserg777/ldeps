<template>
  <div class="sm" ref="root">
    <div class="sm-input">
      <input
        ref="input"
        type="text"
        class="sm-control"
        :placeholder="placeholder"
        v-model="query"
        @focus="openPanel"
        @keydown.esc.prevent="closePanel"
      />
      <button class="sm-btn" @click="openPanel" aria-label="Search">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0aa06e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      </button>
    </div>

    <transition name="sm-fade">
      <div v-if="open" class="sm-panel" :style="panelStyle">
        <div class="sm-inner">
          <div class="sm-col sm-products">
            <div class="sm-title">Товары</div>
            <div v-if="loading" class="sm-skeleton">
              <div class="sm-item" v-for="i in 5" :key="'p'+i">
                <div class="sk-img"/><div class="sk-line"/>
              </div>
            </div>
            <template v-else>
              <a v-for="p in results.products" :key="'p'+p.id" class="sm-item" :href="p.url">
                <img :src="p.image" :alt="p.name" class="sm-thumb" loading="lazy"/>
                <div class="sm-text">{{ p.name }}</div>
              </a>
            </template>
          </div>
          <div class="sm-col">
            <div class="sm-title">Категории</div>
            <ul class="sm-list">
              <li v-for="c in results.categories" :key="'c'+c.id" class="sm-li">
                <span class="sm-bullet"/> <a :href="c.url">{{ c.name }}</a>
              </li>
            </ul>
          </div>
          <div class="sm-col">
            <div class="sm-title">Производители</div>
            <ul class="sm-list">
              <li v-for="m in results.manufacturers" :key="'m'+m.id" class="sm-li">
                <span class="sm-bullet"/> <a :href="m.url">{{ m.name }}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  apiUrl: { type: String, default: '/api/search' },
  placeholder: { type: String, default: 'Поиск оборудования' },
  minChars: { type: Number, default: 2 },
  debounceMs: { type: Number, default: 250 }
})

const root = ref(null)
const input = ref(null)
const open = ref(false)
const query = ref('')
const loading = ref(false)
const results = reactive({ products: [], categories: [], manufacturers: [] })
const panelStyle = ref({})

function openPanel() {
  open.value = true
  updatePanelPlacement()
}
function closePanel() { open.value = false }

function updatePanelPlacement() {
  const el = root.value
  if (!el) return
  const rect = el.getBoundingClientRect()
  panelStyle.value = {
    position: 'fixed',
    top: Math.round(rect.bottom) + 'px',
    left: Math.round(rect.left) + 'px',
    width: Math.round(rect.width) + 'px'
  }
}

let t
watch(query, async (val) => {
  if (!open.value) open.value = true
  if (t) clearTimeout(t)
  t = setTimeout(async () => {
    if (!val || val.length < props.minChars) {
      results.products = []; results.categories = []; results.manufacturers = []
      return
    }
    loading.value = true
    try {
      const res = await fetch(`${props.apiUrl}?q=${encodeURIComponent(val)}`)
      if (res.ok) {
        const data = await res.json()
        // нормализация
        results.products = (data.products || []).map(p => ({
          id: p.id || p.product_id,
          name: p.name,
          image: p.image || p.thumbnail_url,
          url: p.url
        }))
        results.categories = data.categories || []
        results.manufacturers = data.manufacturers || []
      }
    } catch (e) {}
    finally { loading.value = false }
  }, props.debounceMs)
})

function onClickOutside(e) {
  if (!root.value) return
  if (!root.value.contains(e.target)) closePanel()
}

onMounted(() => {
  window.addEventListener('resize', updatePanelPlacement)
  window.addEventListener('scroll', updatePanelPlacement, true)
  document.addEventListener('click', onClickOutside)
})
onBeforeUnmount(() => {
  window.removeEventListener('resize', updatePanelPlacement)
  window.removeEventListener('scroll', updatePanelPlacement, true)
  document.removeEventListener('click', onClickOutside)
})
</script>

<style>
.sm { position: relative; }
.sm-input { display:flex; align-items:center; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.04); }
.sm-control { flex:1; border:0; outline:0; font-size:18px; color:#111827; }
.sm-control::placeholder { color:#94a3b8; }
.sm-btn { border:0; background:#f8fafc; width:46px; height:46px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; }

.sm-panel { z-index: 2000; }
.sm-inner { background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 20px 50px rgba(0,0,0,0.15); padding:16px; display:grid; grid-template-columns: 1.5fr 1fr 1fr; gap:24px; max-height:60vh; overflow:auto; }
.sm-title { font-weight:700; margin-bottom:10px; }

.sm-products .sm-item { display:flex; align-items:center; gap:12px; padding:10px 8px; border-radius:8px; text-decoration:none; color:#111827; }
.sm-products .sm-item:hover { background:#f3f4f6; }
.sm-thumb { width:64px; height:64px; object-fit:contain; background:#f8fafc; border-radius:6px; }
.sm-text { line-height:1.25; }

.sm-list { list-style:none; padding:0; margin:0; }
.sm-li { padding:8px 0; display:flex; align-items:center; gap:8px; }
.sm-bullet { width:8px; height:8px; background:#059669; border-radius:999px; display:inline-block; }

/* skeleton */
.sm-skeleton .sm-item { display:flex; align-items:center; gap:12px; padding:10px 8px; }
.sk-img { width:64px; height:64px; border-radius:6px; background:linear-gradient(90deg,#eee 25%,#f5f5f5 37%,#eee 63%); background-size:400% 100%; animation:sm-shimmer 1.4s ease infinite; }
.sk-line { height:14px; width:60%; border-radius:6px; background:linear-gradient(90deg,#eee 25%,#f5f5f5 37%,#eee 63%); background-size:400% 100%; animation:sm-shimmer 1.4s ease infinite; }
@keyframes sm-shimmer { 0%{background-position:100% 0;}100%{background-position:-100% 0;} }

.sm-fade-enter-active,.sm-fade-leave-active{ transition: opacity .15s ease; }
.sm-fade-enter-from,.sm-fade-leave-to{ opacity:0; }
</style>


