@if($products->hasPages())
    <div class="pagination-container">
        {{-- Show More Button --}}
        @if($products->hasMorePages())
            <div class="show-more-section">
                <button class="btn btn-outline-success show-more-btn" onclick="loadMoreProducts()">
                    <i class="fas fa-sync-alt me-2"></i>
                    Показати ще
                </button>
            </div>
        @else
            <div class="show-more-section">
                <div class="text-muted">
                    <i class="fas fa-check-circle me-2"></i>
                    Всі товари завантажено
                </div>
            </div>
        @endif

        {{-- Pagination Numbers --}}
        <nav aria-label="Page navigation" class="pagination-nav">
            <ul class="pagination justify-content-center">
                {{-- Previous Page Link --}}
                @if ($products->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $products->previousPageUrl() }}" rel="prev">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements - Show only current page and nearby pages --}}
                @php
                    $currentPage = $products->currentPage();
                    $lastPage = $products->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp

                @for($page = $startPage; $page <= $endPage; $page++)
                    @if ($page == $currentPage)
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $products->url($page) }}">{{ $page }}</a>
                        </li>
                    @endif
                @endfor

                {{-- Next Page Link --}}
                @if ($products->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $products->nextPageUrl() }}" rel="next">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>

        {{-- Results Info --}}
        <div class="text-center text-muted mt-3">
            <small>
                Показано {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} 
                з {{ $products->total() }} товарів
            </small>
        </div>
    </div>

    <style>
        .pagination-container {
            margin-top: 2rem;
        }
        
        .show-more-section {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .show-more-btn {
            background: #f8f9fa;
            border: 2px solid #28a745;
            color: #28a745;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .show-more-btn:hover {
            background: #28a745;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }
        
        .pagination-nav {
            margin-bottom: 1rem;
        }
        
        .pagination {
            margin-bottom: 0;
        }
        
        .page-link {
            border: none;
            background: #f8f9fa;
            color: #6c757d;
            margin: 0 2px;
            border-radius: 8px;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }
        
        .page-link:hover {
            background: #e9ecef;
            color: #495057;
            transform: translateY(-1px);
        }
        
        .page-item.active .page-link {
            background: #28a745;
            color: white;
            border-radius: 8px;
        }
        
        .page-item.disabled .page-link {
            background: #f8f9fa;
            color: #adb5bd;
        }
    </style>
@endif
