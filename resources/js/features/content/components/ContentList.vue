<template>
  <div class="content-list-component">
    <div class="container py-4">
      <div class="row">
        <div class="col-12">
          <div v-if="linkParams?.option === 'com_content' && linkParams?.view === 'content'" class="content-list">
            <!-- Articles list -->
            <div class="row">
              <div v-for="article in articles" :key="article.id" class="col-md-6 col-lg-4 mb-4">
                <div class="card article-preview">
                  <div v-if="article.image" class="card-img-top">
                    <img :src="article.image" :alt="article.title" class="img-fluid" style="height: 200px; object-fit: cover;">
                  </div>
                  <div class="card-body">
                    <h5 class="card-title">{{ article.title }}</h5>
                    <div v-if="article.description" class="card-text" v-html="article.description"></div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <a :href="getArticleUrl(article)" class="btn btn-primary btn-sm">
                        Читать далее
                      </a>
                      <small class="text-muted" v-if="article.created">
                        {{ new Date(article.created).toLocaleDateString() }}
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-if="articles.length === 0" class="alert alert-info">
              <p>Статьи не найдены.</p>
            </div>
            
            <!-- Pagination -->
            <Pagination 
              :pagination="pagination"
              :info-text="`Показано ${articles.length} из ${pagination?.total || 0} статей`"
            />
          </div>
          
          <div v-else class="alert alert-warning">
            <h5>Неверные параметры для списка статей</h5>
            <p>Ожидается: option=com_content&view=content</p>
            <p>Получено: {{ JSON.stringify(linkParams) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
import { Pagination } from '../../common/components'

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
  articles: {
    type: Array,
    default: () => []
  },
  pagination: {
    type: Object,
    default: null
  }
})

// Generate article URL based on level parameter
const getArticleUrl = (article) => {
  // If level=1, use only the current menu item path
  if (props.menuItem?.level === 1) {
    return `/${props.menuItem.path}/${article.id}.html`
  }
  // Otherwise use article's own path
  return `/${article.path || article.alias}.html`
}

// Use articles from props
console.log('ContentList mounted with articles:', props.articles)
</script>

<style scoped>
.content-list-component {
  min-height: 400px;
}

.article-preview {
  height: 100%;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.article-preview:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
