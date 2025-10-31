<div x-data="{
        q: '',
        open: false,
        loading: false,
        results: { products: [], categories: [], manufacturers: [] },
        async search() {
            const term = this.q.trim();
            if (term.length < 2) { this.results = {products:[],categories:[],manufacturers:[]}; this.open = false; return; }
            this.loading = true; this.open = true;
            try {
                const res = await fetch(`/api/search?q=${encodeURIComponent(term)}`);
                if (res.ok) { this.results = await res.json(); }
            } catch(e) { /* noop */ }
            this.loading = false;
        }
    }" class="position-relative">
    <div class="input-group">
        <input type="search" class="form-control" placeholder="Поиск оборудования" x-model.debounce.300ms="q" @input="search()" @focus="open = q.length >= 2">
        <button class="btn btn-outline-secondary" type="button" @click="search()"><i class="fas fa-search"></i></button>
    </div>
    <div class="position-absolute bg-white border rounded shadow p-3" x-show="open" @click.outside="open = false" style="top: 110%; left: 0; right: 0; z-index: 5000;">
        <template x-if="loading">
            <div class="text-center py-2">Идёт поиск…</div>
        </template>
        <template x-if="!loading">
            <div class="row g-3">
                <div class="col-12">
                    <h6 class="mb-2">Товары</h6>
                    <div class="list-group">
                        <template x-for="p in (results.products || []).slice(0,5)" :key="p.id">
                            <a class="list-group-item list-group-item-action" :href="p.url" x-text="p.name"></a>
                        </template>
                        <div class="text-muted small" x-show="(results.products||[]).length === 0">Ничего не найдено</div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>


