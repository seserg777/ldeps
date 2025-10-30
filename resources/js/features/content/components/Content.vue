<template>
  <div class="content-component">
    <div class="container py-4">
      <div class="row">
        <div class="col-12">
          
          <div v-if="linkParams?.option === 'com_content' && linkParams?.view === 'article' && article" class="content-body">
            <!-- Article Image -->
            <div v-if="article.image" class="article-image mb-4">
              <img :src="article.image" :alt="article.title" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
            </div>
            
            <!-- Article Title (hidden to avoid duplicate with page H1) -->
            <!-- <h2 class="article-title mb-3">{{ article.title }}</h2> -->
            
            <!-- Article Description -->
            <div v-if="article.description" class="article-description mb-4" v-html="article.description"></div>
            
            <!-- Article Full Text -->
            <div v-if="article.fulltext" class="article-fulltext mb-4" v-html="article.fulltext"></div>
            
            <!-- Article Meta Info -->
            <div class="article-meta text-muted">
              <small>
                <span v-if="article.created">Created: {{ new Date(article.created).toLocaleDateString() }}</span>
                <span v-if="article.id"> | ID: {{ article.id }}</span>
              </small>
            </div>
          </div>
          
          <div v-else-if="linkParams?.option === 'com_content' && linkParams?.view === 'article'" class="alert alert-warning">
            <h5>Статья не найдена</h5>
            <p>Информация о статье недоступна.</p>
          </div>
          
          <div v-else class="alert alert-warning">
            <h5>Unknown content type</h5>
            <p>Link parameters: {{ JSON.stringify(linkParams) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  menuItems: {
    type: Array,
    default: () => []
  },
  language: {
    type: String,
    default: 'uk'
  },
  siteName: {
    type: String,
    default: ''
  },
  siteDescription: {
    type: String,
    default: ''
  },
  menuItem: {
    type: Object,
    default: null
  },
  linkParams: {
    type: Object,
    default: null
  },
  article: {
    type: Object,
    default: null
  }
})

// Props are ready for use
</script>

<style scoped>
.content-component {
  min-height: 400px;
}

.content-body {
  background: transparent;
  padding: 0;
}

.alert {
  padding: 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 4px;
}

.alert-info {
  color: #0c5460;
  background-color: #d1ecf1;
  border-color: #bee5eb;
}

.alert-warning {
  color: #856404;
  background-color: #fff3cd;
  border-color: #ffeaa7;
}
</style>



