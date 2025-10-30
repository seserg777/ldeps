<template>
  <nav :class="['site-menu', `menu-layout-${layout}`, `menu-${menutype}`]">
    <div v-if="loading" class="menu-skeleton">
      <span class="skeleton-pill" v-for="i in 6" :key="i" />
    </div>
    <div v-else-if="error" class="text-center py-2 text-danger">
      <i class="fas fa-exclamation-triangle"></i> {{ error }}
    </div>
    <div v-else-if="html" v-html="html"></div>
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
const html = ref('')
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
    } else {
      // Try server-rendered HTML endpoint first for SEO-friendly content
      const url = `/menu/html/${encodeURIComponent(props.menutype)}?maxLevels=${encodeURIComponent(props.maxLevels)}&language=${encodeURIComponent(props.language || '')}`
      const res = await fetch(url, { headers: { 'Accept': 'text/html' } })
      if (res.ok) {
        html.value = await res.text()
      } else {
        // Fallback: clear html; keep items empty
        html.value = ''
        error.value = 'Network error'
      }
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

<style>
.site-menu { 
  width: 100%; 
  position: relative; /* create stacking context for dropdowns */
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
  gap: 24px;
}

.menu-layout-default .menu-root {
  justify-content: flex-start;
  flex-wrap: nowrap;
  white-space: nowrap;
}

/* Spacing & look for top navigation */
.menu-root > .menu-item { position: relative; }
.menu-link {
  display: inline-block;
  padding: 12px 8px;
  color: #1f2937; /* gray-800 */
  text-decoration: none;
  font-weight: 600;
}
.menu-link:hover { color: #0d6efd; text-decoration: none; }
.menu-link.active { border-bottom: 2px solid #0d6efd; }

/* Generic dropdown: hide nested ULs by default and show on hover */
.site-menu .menu-level .menu-level { 
  display: none; /* hidden by default */
  position: absolute;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  min-width: 220px;
  padding: 10px 0;
  z-index: 4500; /* ensure above page content */
  flex-direction: column; /* nested menus are vertical */
  gap: 0; /* remove spacing between items */
}
.site-menu .menu-level .menu-item {position: relative;}
.site-menu .menu-level .menu-level > .menu-item { display: block;position: relative; }
.site-menu .menu-level.level-1 > .menu-item > .menu-level { top: 100%; left: 0; }
.site-menu .menu-level.level-2 > .menu-item > .menu-level { top: 0; left: 100%; }
.site-menu .menu-level.level-3 > .menu-item > .menu-level { top: 0; left: 100%; }
.menu-item:hover > .menu-level { display: block; }
/* small offset so panel doesn't overlap the toggler */
.site-menu .menu-level.level-1 > .menu-item > .menu-level { margin-top: 0;z-index: 9; }

/* Mega dropdown for level-1 if used */
.menu-mega {
  position: absolute;
  top: 100%;
  left: 0;
  right: auto;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.12);
  padding: 16px;
  display: none;
  min-width: 640px;
  z-index: 4500; /* above content */
}
.menu-item.level-1:hover > .menu-mega { display: block; }

/* Caret indicator for dropdowns */
.menu-item.level-1.has-children > .menu-link:after {
  content: '';
  display: inline-block;
  width: 0; height: 0;
  margin-left: 6px;
  vertical-align: middle;
  border-left: 4px solid transparent;
  border-right: 4px solid transparent;
  border-top: 5px solid #9ca3af; /* gray-400 */
}
.menu-item.level-1.has-children:hover > .menu-link:after { border-top-color: #0d6efd; }

/* Right-facing caret for nested items with children */
.menu-item.level-2.has-children > .menu-link:after,
.menu-item.level-3.has-children > .menu-link:after,
.menu-item.level-4.has-children > .menu-link:after {
  content: '';
  display: inline-block;
  width: 0; height: 0;
  margin-left: 6px;
  vertical-align: middle;
  border-top: 4px solid transparent;
  border-bottom: 4px solid transparent;
  border-left: 5px solid #9ca3af;
}
.menu-item.level-2.has-children:hover > .menu-link:after,
.menu-item.level-3.has-children:hover > .menu-link:after,
.menu-item.level-4.has-children:hover > .menu-link:after { border-left-color: #0d6efd; }

/* Skeleton for menu */
.menu-skeleton { display: flex; gap: 16px; align-items: center; min-height: 44px; /* match menu row height to avoid CLS */ }
.skeleton-pill { width: 80px; height: 14px; border-radius: 999px; background: linear-gradient(90deg,#eee 25%,#f5f5f5 37%,#eee 63%); background-size: 400% 100%; animation: shimmer 1.4s ease infinite; }
@keyframes shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }

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

