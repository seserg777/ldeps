<template>
  <div class="content-universal">
    <!-- Skeleton while fetching meta/data -->
    <div v-if="isLoading" class="skeleton">
      <div class="skeleton-line w-60 mb-2" />
      <div class="skeleton-line w-90 mb-2" />
      <div class="skeleton-line w-75" />
    </div>

    <!-- Render resolved child component based on linkParams -->
    <component
      v-else-if="resolvedComponent"
      :is="resolvedComponent"
      :language="language"
      :menu-item="menuItem"
      :link-params="linkParams"
      :article="article"
      :articles="articles"
      :pagination="pagination"
    />
    <div v-else class="alert alert-warning">
      <h5>Unknown content mode</h5>
      <p>Link parameters: {{ JSON.stringify(linkParams) }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { Article, ArticleList } from '../../article/components'
import { Exussalebanner, ExussalebannerList } from '../../promo/components'
import JshoppingCategory from '../../jshopping/components/JshoppingCategory.vue'

const props = defineProps({
  language: { type: String, default: 'uk' },
  menuItem: { type: Object, default: null },
  linkParams: { type: Object, default: null },
  article: { type: Object, default: null },
  articles: { type: Array, default: () => [] },
  pagination: { type: Object, default: null }
})

// Local reactive state when props are not provided
const meta = ref({
  menuItem: null,
  linkParams: null,
  article: null,
  articles: [],
  pagination: null
})
const isLoading = ref(false)

onMounted(async () => {
  if (!props.linkParams) {
    // Resolve page alias from URL: /alias.html
    let path = (window.location.pathname || '/').replace(/^\//, '')
    if (path.endsWith('.html')) path = path.slice(0, -5)
    try {
      isLoading.value = true
      const res = await fetch(`/api/page-meta/${encodeURIComponent(path)}`)
      if (res.ok) {
        const json = await res.json()
        meta.value.menuItem = json.menuItem || null
        meta.value.linkParams = json.linkParams || null
        if (json.additionalData && json.additionalData.article) {
          meta.value.article = json.additionalData.article
        }
      }
    } catch (e) { /* silent */ }
    finally { isLoading.value = false }
  }
})

const effectiveLinkParams = computed(() => props.linkParams || meta.value.linkParams)
const effectiveArticle = computed(() => props.article || meta.value.article)
const effectiveMenuItem = computed(() => props.menuItem || meta.value.menuItem)
const effectiveArticles = computed(() => props.articles || meta.value.articles)
const effectivePagination = computed(() => props.pagination || meta.value.pagination)

// Derive mode: 'article' for view=article, 'list' for view=list/content
const resolvedComponent = computed(() => {
  const lp = effectiveLinkParams.value
  const option = lp?.option
  const view = (lp?.view || '').toLowerCase()
  if (option === 'com_content') {
    if (view === 'article') return Article
    if (view === 'content' || view === 'list' || view === 'category') return ArticleList
  }
  if (option === 'com_jshopping') {
    if (view === 'category') return JshoppingCategory
  }
  if (option === 'com_exussalebanner') {
    if (view === 'exussalebanner') return ExussalebannerList
    if (view === 'banner' || view === 'item') return Exussalebanner
  }
  return null
})
</script>

<style scoped>
.content-universal { min-height: 300px; }
.alert { padding: 15px; border: 1px solid #ffeaa7; background: #fff3cd; color: #856404; border-radius: 4px; }
/* Lightweight skeleton shimmer */
.skeleton-line { height: 14px; background: linear-gradient(90deg,#eee 25%,#f5f5f5 37%,#eee 63%); background-size: 400% 100%; animation: shimmer 1.4s ease infinite; border-radius: 6px; }
.skeleton-line.mb-2 { margin-bottom: 8px; }
.skeleton-line.w-60 { width: 60%; }
.skeleton-line.w-75 { width: 75%; }
.skeleton-line.w-90 { width: 90%; }
@keyframes shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }
</style>


