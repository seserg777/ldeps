<template>
  <nav :class="['site-menu', `menu-layout-${layout}`]">
    <div v-if="loading" class="text-center py-2">
      <i class="fas fa-spinner fa-spin"></i> Завантаження...
    </div>
    <div v-else-if="error" class="text-center py-2 text-danger">
      <i class="fas fa-exclamation-triangle"></i> {{ error }}
    </div>
    <ul v-else :class="['menu-level', 'menu-root', `menu-layout-${layout}`]">
      <MenuItem
        v-for="item in items"
        :key="item.id"
        :item="item"
        :level="1"
        :layout="layout"
        :language="language"
        :max-levels="maxLevels"
      />
    </ul>
  </nav>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import MenuItem from './MenuItem.vue'

const props = defineProps({
  menutype: { type: String, required: true },
  apiUrl: { type: String, default: '' },
  items: { type: Array, default: () => [] },
  layout: { 
    type: String, 
    default: 'default',
    validator: (value) => ['default', 'vertical', 'compact'].includes(value)
  },
  language: { 
    type: String, 
    default: 'ru-UA',
    description: 'Language code for filtering menu items (e.g., "ru-UA", "en-US", "uk-UA")'
  },
  // Maximum levels (depth) to render, including the root level. Default 1 shows only top-level items
  maxLevels: {
    type: Number,
    default: 1,
  }
})

const items = ref(Array.isArray(props.items) ? props.items : [])
const loading = ref(false)
const error = ref(null)

// Filter items by language
// Accepts both full locales (e.g., "ru-UA") and base languages (e.g., "ru")
// If no language property is set, item is included by default (backward compatibility)
const normalizeLang = (lang) => (lang || '').toString().toLowerCase().replace('_', '-')
const languageMatches = (itemLang, desiredLang) => {
  const a = normalizeLang(itemLang)
  const b = normalizeLang(desiredLang)
  if (!a || !b) return true
  // Match by base language (before '-')
  const aBase = a.split('-')[0]
  const bBase = b.split('-')[0]
  return a === b || aBase === bBase
}

const filterItemsByLanguage = (items, language) => {
  if (!Array.isArray(items)) return []
  
  return items.filter(item => {
    // Respect menu_show flag when present
    if (item.menu_show === 0 || item.menu_show === '0') {
      return false
    }
    // If item has language property, check if it matches
    if (item.language) {
      return languageMatches(item.language, language)
    }
    // If no language property, include by default (for backward compatibility)
    return true
  }).map(item => ({
    ...item,
    children: item.children ? filterItemsByLanguage(item.children, language) : []
  }))
}

const resolveApiUrl = () => {
  if (props.apiUrl) return props.apiUrl
  return `/api/menu/${encodeURIComponent(props.menutype)}`
}

async function loadMenu() {
  console.log('Menu: loadMenu called with props:', { menutype: props.menutype, itemsCount: props.items?.length, apiUrl: props.apiUrl, language: props.language })
  loading.value = true
  error.value = null
  try {
    // If items are already provided via props, use them and skip fetching
    if (props.items && props.items.length > 0) {
      console.log('Menu: Using provided items:', props.items.length)
      // Filter items by language
      const filteredItems = filterItemsByLanguage(props.items, props.language)
      items.value = filteredItems
      console.log('Menu: Filtered items by language:', filteredItems.length)
    } else if (props.apiUrl) {
      const url = resolveApiUrl()
      const res = await fetch(url)
      const data = await res.json()
      if (data && data.success) {
        items.value = Array.isArray(data.items) ? data.items : []
      } else {
        items.value = []
        error.value = 'Failed to load menu'
      }
    } else {
      items.value = []
    }
  } catch (e) {
    error.value = 'Network error'
    items.value = []
  } finally {
    loading.value = false
  }
}

watch(() => props.menutype, () => loadMenu())
onMounted(() => {
  console.log('Menu: Component mounted, initial props:', { menutype: props.menutype, itemsCount: props.items?.length, apiUrl: props.apiUrl })
  loadMenu()
})

</script>

<style scoped>
.site-menu { 
  width: 100%; 
  position: relative; /* create stacking context for dropdowns */
  z-index: 2000; /* keep above following header strip */
}

/* 
 * Layout options:
 * - default: Horizontal menu with light background
 * - vertical: Vertical menu with white background and shadow
 * - compact: Compact horizontal menu with blue accent
 */
.menu-layout-default .menu-level { 
  list-style: none; 
  margin: 0; 
  padding: 0; 
  display: flex;
  align-items: center;
  gap: 0;
}

.menu-layout-default .menu-root {
  justify-content: flex-start;
  flex-wrap: wrap;
}

/* Vertical layout */
.menu-layout-vertical {
  background: #ffffff;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.menu-layout-vertical .menu-level { 
  list-style: none; 
  margin: 0; 
  padding: 0; 
  display: flex;
  flex-direction: column;
}

.menu-layout-vertical .menu-root {
  width: 100%;
}

/* Compact layout */
.menu-layout-compact {
  background: #ffffff;
  border-bottom: 2px solid #0d6efd;
}

.menu-layout-compact .menu-level { 
  list-style: none; 
  margin: 0; 
  padding: 0; 
  display: flex;
  align-items: center;
  gap: 0;
}

.menu-layout-compact .menu-root {
  justify-content: center;
  flex-wrap: wrap;
}
</style>

