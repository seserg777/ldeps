@if(isset($childCategories) && $childCategories->count() > 0)
    <div class="child-categories-section mb-5">
        <!-- Current Category Title -->
        <div class="current-category-header mb-4">
            <h2 class="current-category-title">{{ $category->name }}</h2>
        </div>
        
        <!-- Categories Grid -->
        <div class="categories-grid">
            <div class="row">
                @foreach($childCategories as $childCategory)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="category-column">
                            <!-- First Level Category -->
                            <div class="first-level-category">
                                <h4 class="category-title">
                                    <a href="{{ route('category.show', $childCategory->full_path) }}" class="category-link">
                                        {{ $childCategory->name }}
                                    </a>
                                </h4>
                            </div>
                            
                            <!-- Second Level Categories and Complexes -->
                            @php
                                $secondLevelCategories = $childCategory->subcategories ?? collect();
                                $complexes = $childCategory->complexes ?? collect();
                            @endphp
                            @if($secondLevelCategories->count() > 0 || $complexes->count() > 0)
                                <div class="second-level-categories">
                                    <ul class="subcategory-list">
                                        @foreach($secondLevelCategories as $secondLevel)
                                            <li class="subcategory-item">
                                                <a href="{{ route('category.show', $secondLevel->full_path) }}" class="subcategory-link">
                                                    {{ $secondLevel->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                        @foreach($complexes as $complex)
                                            <li class="complex-item">
                                                <a href="{{ $complex->complex_url }}" class="complex-link">
                                                    {{ $complex->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="no-subcategories">
                                    <p class="text-muted small">Немає підкатегорій</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

@push('styles')
<style>
    .child-categories-section {
        background: #fff;
        border-radius: 8px;
        padding: 30px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    /* Current Category Header */
    .current-category-header {
        text-align: center;
        border-bottom: 2px solid #f8f9fa;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    
    .current-category-title {
        color: #2c3e50;
        font-weight: 700;
        font-size: 1.8rem;
        margin: 0;
    }
    
    /* Categories Grid */
    .categories-grid {
        margin-top: 20px;
    }
    
    /* Category Column */
    .category-column {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 20px;
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .category-column:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: #007bff;
    }
    
    /* First Level Category */
    .first-level-category {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .category-title {
        margin: 0 0 8px 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    
    .category-link {
        color: #007bff;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .category-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }
    
    
    /* Second Level Categories */
    .second-level-categories {
        margin-top: 10px;
    }
    
    .subcategory-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .subcategory-item {
        margin-bottom: 8px;
    }
    
    .subcategory-link {
        color: #495057;
        text-decoration: none;
        font-size: 0.9rem;
        line-height: 1.4;
        display: block;
        padding: 4px 0;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
        padding-left: 8px;
    }
    
    .subcategory-link:hover {
        color: #007bff;
        background: rgba(0, 123, 255, 0.05);
        border-left-color: #007bff;
        padding-left: 12px;
    }
    
    /* Complex styles */
    .complex-item {
        margin-bottom: 8px;
    }
    
    .complex-link {
        color: #28a745;
        text-decoration: none;
        font-size: 0.9rem;
        line-height: 1.4;
        display: block;
        padding: 4px 0;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
        padding-left: 8px;
        font-weight: 500;
    }
    
    .complex-link:hover {
        color: #1e7e34;
        background: rgba(40, 167, 69, 0.05);
        border-left-color: #28a745;
        padding-left: 12px;
    }
    
    .no-subcategories {
        text-align: center;
        padding: 15px 0;
    }
    
    .no-subcategories p {
        margin: 0;
        font-style: italic;
    }
    
    /* Animation for category columns */
    .category-column {
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
        transform: translateY(20px);
    }
    
    .category-column:nth-child(1) { animation-delay: 0.1s; }
    .category-column:nth-child(2) { animation-delay: 0.2s; }
    .category-column:nth-child(3) { animation-delay: 0.3s; }
    .category-column:nth-child(4) { animation-delay: 0.4s; }
    .category-column:nth-child(5) { animation-delay: 0.5s; }
    .category-column:nth-child(6) { animation-delay: 0.6s; }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .category-column {
            margin-bottom: 20px;
        }
    }
    
    @media (max-width: 768px) {
        .child-categories-section {
            padding: 20px;
        }
        
        .current-category-title {
            font-size: 1.5rem;
        }
        
        .category-column {
            padding: 15px;
        }
        
        .category-title {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .child-categories-section {
            padding: 15px;
            border-radius: 6px;
        }
        
        .current-category-title {
            font-size: 1.3rem;
        }
        
        .category-column {
            padding: 12px;
            border-radius: 4px;
        }
        
        .subcategory-link {
            font-size: 0.85rem;
        }
    }
</style>
@endpush
