{{-- Search Module Type --}}
@php
    $params = $module->params_array;
    $placeholder = $params['placeholder'] ?? 'Search...';
    $showButton = $params['show_button'] ?? true;
@endphp

<div class="module-search" x-data="{ 
    query: '', 
    results: [], 
    loading: false,
    showResults: false,
    async search() {
        if (this.query.length < 2) {
            this.results = [];
            this.showResults = false;
            return;
        }
        this.loading = true;
        try {
            const res = await fetch(`/products/search?q=${encodeURIComponent(this.query)}`);
            const data = await res.json();
            this.results = [
                ...(data.products || []),
                ...(data.categories || []),
            ];
            this.showResults = true;
        } catch (e) {
            console.error('Search error:', e);
            this.results = [];
        }
        this.loading = false;
    }
}" @click.away="showResults = false">
    <form action="{{ route('products.index') }}" 
          method="GET" 
          class="search-form"
          @submit.prevent="if(query) $el.submit()">
        <div class="search-input-wrapper">
            <input type="text" 
                   name="search" 
                   x-model="query"
                   @input.debounce.300ms="search()"
                   @focus="if(results.length) showResults = true"
                   class="search-input" 
                   placeholder="{{ $placeholder }}"
                   autocomplete="off">
            
            @if($showButton)
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            @endif
        </div>
        
        <!-- Search Results Dropdown -->
        <div x-show="showResults && (results.length > 0 || loading)" 
             x-transition
             class="search-results">
            <template x-if="loading">
                <div class="search-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Searching...</span>
                </div>
            </template>
            
            <template x-if="!loading && results.length > 0">
                <ul class="search-results-list">
                    <template x-for="item in results" :key="item.id">
                        <li class="search-result-item">
                            <a :href="item.url" class="search-result-link">
                                <template x-if="item.image">
                                    <img :src="item.image" 
                                         :alt="item.name || item.title"
                                         class="search-result-image">
                                </template>
                                <div class="search-result-info">
                                    <span class="search-result-name" x-text="item.name || item.title"></span>
                                    <template x-if="item.price">
                                        <span class="search-result-price" x-text="item.price"></span>
                                    </template>
                                </div>
                            </a>
                        </li>
                    </template>
                </ul>
            </template>
        </div>
    </form>
</div>

