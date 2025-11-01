{{-- Articles/Content Module Type --}}
@php
    $params = $module->params_array;
    $limit = (int)($params['limit'] ?? 5);
    $categoryId = $params['category_id'] ?? null;
    $featured = $params['featured'] ?? false;
    
    // Build articles query
    $query = \App\Models\Content\Content::where('state', 'published');
    
    if ($categoryId) {
        $query->where('catid', $categoryId);
    }
    
    if ($featured) {
        $query->where('featured', 1);
    }
    
    $articles = $query->orderBy('created_at', 'desc')->limit($limit)->get();
@endphp

@if($articles->count() > 0)
    <div class="module-articles">
        <ul class="articles-list">
            @foreach($articles as $article)
                <li class="article-item">
                    <a href="{{ route('content.show', $article->id) }}" class="article-link">
                        @if($article->images && isset($article->images['image_intro']))
                            <div class="article-image">
                                <img src="{{ $article->images['image_intro'] }}" 
                                     alt="{{ $article->title }}">
                            </div>
                        @endif
                        <div class="article-info">
                            <h4 class="article-title">{{ $article->title }}</h4>
                            @if($article->introtext)
                                <p class="article-intro">{{ \Illuminate\Support\Str::limit(strip_tags($article->introtext), 100) }}</p>
                            @endif
                            <span class="article-date">{{ $article->created_at->format('d.m.Y') }}</span>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif

