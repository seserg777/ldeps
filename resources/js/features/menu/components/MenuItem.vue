<template>
  <li 
    :class="['menu-item', 'level-' + level, `menu-layout-${layout}`, { 'has-children': item.children && item.children.length, active: isActive }]"
    @mouseenter="showSubmenu"
    @mouseleave="hideSubmenu"
  >
    <a :href="generateUrl(item)" class="menu-link" :class="{ active: isActive }">
      <span class="menu-title">{{ item.title }}</span>
      <i v-if="item.children && item.children.length" class="menu-arrow fas fa-chevron-down"></i>
    </a>
    
    <!-- Dropdown submenu -->
    <ul 
      v-if="item.children && item.children.length" 
      class="menu-submenu"
      :class="{ 'show': isSubmenuVisible }"
    >
      <MenuItem v-for="child in item.children" :key="child.id" :item="child" :level="level + 1" :layout="layout" :language="language" />
    </ul>
  </li>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  item: { type: Object, required: true },
  level: { type: Number, default: 1 },
  layout: { type: String, default: 'default' },
  language: { type: String, default: 'ru-UA' }
})

const isSubmenuVisible = ref(false)

// Detect if this item corresponds to current page
const isActive = computed(() => {
  const current = (window.location && window.location.pathname) ? window.location.pathname.replace(/\/$/, '') : ''
  const path = (props.item && props.item.path) ? String(props.item.path).trim() : ''
  if (!path) return false
  const candidates = [
    `/${path}`,
    `/${path}.html`
  ]
  // Match exact, and nested paths like /promo/449
  return candidates.includes(current) || current.startsWith(`/${path}/`)
})

// Parse link parameters to determine component type
const parseLinkParams = (link) => {
  if (!link || typeof link !== 'string') return null
  
  try {
    const url = new URL(link, window.location.origin)
    const params = new URLSearchParams(url.search)
    
    return {
      option: params.get('option'),
      view: params.get('view'),
      id: params.get('id'),
      task: params.get('task')
    }
  } catch (e) {
    return null
  }
}

// Generate SEO-friendly URL based on path parameter
const generateUrl = (item) => {
  // If item has path, generate SEO-friendly URL
  if (item.path && item.path.trim()) {
    return `/${item.path}.html`
  }
  
  // If no path, fallback to original link or root
  return item.link || '/'
}

// Determine component type based on link parameters
const getComponentType = (item) => {
  const params = parseLinkParams(item.link)
  if (!params) return null
  
  // Map option parameters to Vue components
  switch (params.option) {
    case 'com_content':
      return 'Content'
    // Add more mappings as needed
    default:
      return null
  }
}

const showSubmenu = () => {
  if (props.level === 1) { // Only show submenu for first level items
    isSubmenuVisible.value = true
  }
}

const hideSubmenu = () => {
  isSubmenuVisible.value = false
}
</script>

<style scoped>
.menu-item { 
  position: relative; 
  display: flex;
  align-items: center;
}

.menu-link { 
  display: flex;
  align-items: center;
  text-decoration: none; 
  color: #495057;
  font-weight: 500;
  font-size: 14px;
  transition: all 0.2s ease;
  border-bottom: 2px solid transparent;
}

.menu-link.active {
  color: #0d6efd;
  border-bottom-color: #0d6efd;
}

/* Default layout styles */
.menu-layout-default .menu-link {
  padding: 12px 16px;
}

.menu-layout-default .menu-link:hover { 
  color: #0d6efd; 
  background-color: rgba(13, 110, 253, 0.05);
  border-bottom-color: #0d6efd;
}

/* Vertical layout styles */
.menu-layout-vertical .menu-link {
  padding: 10px 20px;
  width: 100%;
  border-bottom: 1px solid #f8f9fa;
}

.menu-layout-vertical .menu-link:hover { 
  color: #0d6efd; 
  background-color: #f8f9fa;
  border-bottom-color: #0d6efd;
}

/* Compact layout styles */
.menu-layout-compact .menu-link {
  padding: 8px 12px;
  font-size: 13px;
}

.menu-layout-compact .menu-link:hover { 
  color: #0d6efd; 
  background-color: rgba(13, 110, 253, 0.1);
  border-bottom-color: #0d6efd;
}

.menu-title {
  margin-right: 6px;
}

.menu-arrow {
  font-size: 10px;
  transition: transform 0.2s ease;
}

.menu-item:hover .menu-arrow {
  transform: rotate(180deg);
}

/* Submenu styles */
.menu-submenu {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  min-width: 200px;
  z-index: 3000;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-10px);
  transition: all 0.2s ease;
  list-style: none;
  margin: 0;
  padding: 8px 0;
}

.menu-submenu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.menu-submenu .menu-item {
  display: block;
}

.menu-submenu .menu-link {
  padding: 10px 16px;
  border-bottom: none;
  font-weight: 400;
  font-size: 13px;
}

.menu-submenu .menu-link:hover {
  background-color: #f8f9fa;
  border-bottom-color: transparent;
}

/* Second level submenu positioning */
.menu-submenu .menu-submenu {
  top: 0;
  left: 100%;
  margin-top: -8px;
}

/* Hide submenu arrows for submenu items */
.menu-submenu .menu-arrow {
  display: none;
}
</style>
