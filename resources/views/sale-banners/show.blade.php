@extends('layouts.app')

@section('title', $saleBanner->getLocalizedField('title'))
@section('description', $saleBanner->getLocalizedField('metadesc') ?: Str::limit(strip_tags($saleBanner->getLocalizedField('introtext')), 160))

@section('content')
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Головна</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sale-banners.index') }}">Акції</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $saleBanner->getLocalizedField('title') }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Sale Banner Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            @if($saleBanner->getLocalizedField('image'))
                                <img src="{{ $saleBanner->getLocalizedField('image') }}" 
                                     class="img-fluid rounded" 
                                     alt="{{ $saleBanner->getLocalizedField('title') }}">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-percentage fa-4x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-8">
                            <h1 class="h2 mb-3">{{ $saleBanner->getLocalizedField('title') }}</h1>
                            
                            <!-- Sale Period and Countdown -->
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $saleBanner->sale_start->format('d.m.Y') }} - {{ $saleBanner->sale_end->format('d.m.Y') }}
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        @if($saleBanner->isActive())
                                            <div class="countdown-timer" data-end-date="{{ $saleBanner->sale_end->toISOString() }}">
                                                <div class="countdown-label text-muted small">До кінця акції:</div>
                                                <div class="countdown-display">
                                                    <span class="countdown-item">
                                                        <span class="countdown-number" data-type="days">0</span>
                                                        <span class="countdown-label">дні</span>
                                                    </span>
                                                    <span class="countdown-separator">:</span>
                                                    <span class="countdown-item">
                                                        <span class="countdown-number" data-type="hours">0</span>
                                                        <span class="countdown-label">год</span>
                                                    </span>
                                                    <span class="countdown-separator">:</span>
                                                    <span class="countdown-item">
                                                        <span class="countdown-number" data-type="minutes">0</span>
                                                        <span class="countdown-label">хв</span>
                                                    </span>
                                                    <span class="countdown-separator">:</span>
                                                    <span class="countdown-item">
                                                        <span class="countdown-number" data-type="seconds">0</span>
                                                        <span class="countdown-label">сек</span>
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary fs-6">
                                                <i class="fas fa-check-circle me-1"></i>Акція завершена
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tags -->
                            @if($saleBanner->tags->count() > 0)
                                <div class="mb-3">
                                    @foreach($saleBanner->tags as $tag)
                                        <span class="badge bg-primary me-1">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Intro Text -->
                            @if($saleBanner->getLocalizedField('introtext'))
                                <div class="lead">
                                    {!! $saleBanner->getLocalizedField('introtext') !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Text Content -->
            @if($saleBanner->getLocalizedField('fulltext'))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Детальна інформація
                        </h5>
                    </div>
                    <div class="card-body">
                        {!! $saleBanner->getLocalizedField('fulltext') !!}
                    </div>
                </div>
            @endif

            <!-- Conditions -->
            @if($saleBanner->getLocalizedField('conditions'))
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list-check me-2"></i>Умови акції
                        </h5>
                    </div>
                    <div class="card-body">
                        {!! $saleBanner->getLocalizedField('conditions') !!}
                    </div>
                </div>
            @endif

            <!-- JShop Products -->
            @if($saleBanner->jshopProducts->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart me-2"></i>Товари в акції
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($saleBanner->jshopProducts as $product)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $product->name }}</h6>
                                            
                                            @if($product->hasDiscount())
                                                <div class="mb-2">
                                                    <span class="text-decoration-line-through text-muted me-2">
                                                        {{ number_format($product->oldprice, 2) }} грн
                                                    </span>
                                                    <span class="text-danger fw-bold">
                                                        {{ number_format($product->price, 2) }} грн
                                                    </span>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <span class="badge bg-danger">
                                                        -{{ $product->getDiscountPercentage() }}%
                                                    </span>
                                                    <small class="text-muted ms-2">
                                                        Економія: {{ number_format($product->getDiscountAmount(), 2) }} грн
                                                    </small>
                                                </div>
                                            @else
                                                <div class="mb-2">
                                                    <span class="fw-bold">
                                                        {{ number_format($product->price, 2) }} грн
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- 1C Special Offers -->
            @if($saleBanner->specialOffers->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>Спеціальні пропозиції
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($saleBanner->specialOffers as $offer)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $offer->name }}</h6>
                                            <p class="card-text">
                                                <strong>Кількість:</strong> {{ $offer->qty }}<br>
                                                <strong>1C ключ:</strong> {{ $offer->key_1c }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <!-- Sale Info -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-percentage me-2"></i>Інформація про акцію
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-6">Початок:</dt>
                        <dd class="col-6">{{ $saleBanner->sale_start->format('d.m.Y H:i') }}</dd>
                        
                        <dt class="col-6">Закінчення:</dt>
                        <dd class="col-6">{{ $saleBanner->sale_end->format('d.m.Y H:i') }}</dd>
                        
                        <dt class="col-6">Статус:</dt>
                        <dd class="col-6">
                            @if($saleBanner->isActive())
                                <span class="badge bg-success">Активна</span>
                            @else
                                <span class="badge bg-secondary">Неактивна</span>
                            @endif
                        </dd>
                        
                        <dt class="col-6">Перегляди:</dt>
                        <dd class="col-6">{{ $saleBanner->hits }}</dd>
                    </dl>
                    
                    @if($saleBanner->isActive())
                        <hr>
                        <div class="text-center">
                            <h6 class="text-danger mb-3">
                                <i class="fas fa-clock me-1"></i>До кінця акції
                            </h6>
                            <div class="countdown-timer-sidebar" data-end-date="{{ $saleBanner->sale_end->toISOString() }}">
                                <div class="countdown-display-sidebar">
                                    <div class="countdown-item-sidebar">
                                        <span class="countdown-number-sidebar" data-type="days">0</span>
                                        <span class="countdown-label-sidebar">дні</span>
                                    </div>
                                    <div class="countdown-separator-sidebar">:</div>
                                    <div class="countdown-item-sidebar">
                                        <span class="countdown-number-sidebar" data-type="hours">0</span>
                                        <span class="countdown-label-sidebar">год</span>
                                    </div>
                                    <div class="countdown-separator-sidebar">:</div>
                                    <div class="countdown-item-sidebar">
                                        <span class="countdown-number-sidebar" data-type="minutes">0</span>
                                        <span class="countdown-label-sidebar">хв</span>
                                    </div>
                                    <div class="countdown-separator-sidebar">:</div>
                                    <div class="countdown-item-sidebar">
                                        <span class="countdown-number-sidebar" data-type="seconds">0</span>
                                        <span class="countdown-label-sidebar">сек</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Sale Banners -->
            @if($relatedBanners->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-link me-2"></i>Схожі акції
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($relatedBanners as $related)
                            <div class="d-flex mb-3">
                                @if($related->getLocalizedField('image'))
                                    <img src="{{ $related->getLocalizedField('image') }}" 
                                         class="rounded me-3" 
                                         style="width: 60px; height: 60px; object-fit: cover;"
                                         alt="{{ $related->getLocalizedField('title') }}">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-percentage text-muted"></i>
                                    </div>
                                @endif
                                
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ $related->getUrl() }}" class="text-decoration-none">
                                            {{ $related->getLocalizedField('title') }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        {{ $related->sale_start->format('d.m.Y') }} - {{ $related->sale_end->format('d.m.Y') }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
.countdown-timer {
    text-align: center;
    padding: 15px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    border: 2px solid #dee2e6;
}

.countdown-display {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    margin-top: 10px;
}

.countdown-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 50px;
}

.countdown-number {
    font-size: 1.8rem;
    font-weight: bold;
    color: #dc3545;
    line-height: 1;
    background: white;
    padding: 8px 12px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: 1px solid #dee2e6;
}

.countdown-label {
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 4px;
    font-weight: 500;
}

.countdown-separator {
    font-size: 1.5rem;
    font-weight: bold;
    color: #dc3545;
    margin: 0 5px;
}

.countdown-timer .countdown-label:first-child {
    font-size: 0.9rem;
    color: #495057;
    margin-bottom: 8px;
    font-weight: 600;
}

/* Sidebar countdown styles */
.countdown-timer-sidebar {
    padding: 10px;
    background: linear-gradient(135deg, #fff5f5 0%, #ffe6e6 100%);
    border-radius: 8px;
    border: 1px solid #f8d7da;
}

.countdown-display-sidebar {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 3px;
}

.countdown-item-sidebar {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 35px;
}

.countdown-number-sidebar {
    font-size: 1.2rem;
    font-weight: bold;
    color: #dc3545;
    line-height: 1;
    background: white;
    padding: 4px 6px;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    border: 1px solid #f8d7da;
}

.countdown-label-sidebar {
    font-size: 0.6rem;
    color: #6c757d;
    margin-top: 2px;
    font-weight: 500;
}

.countdown-separator-sidebar {
    font-size: 1rem;
    font-weight: bold;
    color: #dc3545;
    margin: 0 2px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .countdown-display {
        gap: 3px;
    }
    
    .countdown-number {
        font-size: 1.4rem;
        padding: 6px 8px;
    }
    
    .countdown-separator {
        font-size: 1.2rem;
        margin: 0 2px;
    }
    
    .countdown-item {
        min-width: 40px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const countdownTimers = document.querySelectorAll('.countdown-timer, .countdown-timer-sidebar');
    
    if (countdownTimers.length === 0) return;
    
    // Get end date from first timer (all should have same end date)
    const endDate = new Date(countdownTimers[0].dataset.endDate);
    
    function updateCountdown() {
        const now = new Date();
        const timeLeft = endDate - now;
        
        if (timeLeft <= 0) {
            // Sale has ended - update all timers
            countdownTimers.forEach(timer => {
                if (timer.classList.contains('countdown-timer')) {
                    timer.innerHTML = `
                        <div class="countdown-label text-muted small">Акція завершена</div>
                        <div class="countdown-display">
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-check-circle me-1"></i>Час вийшов
                            </span>
                        </div>
                    `;
                } else if (timer.classList.contains('countdown-timer-sidebar')) {
                    timer.innerHTML = `
                        <div class="countdown-display-sidebar">
                            <span class="badge bg-secondary">
                                <i class="fas fa-check-circle me-1"></i>Завершено
                            </span>
                        </div>
                    `;
                }
            });
            return;
        }
        
        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        
        // Update all timers
        countdownTimers.forEach(timer => {
            const daysElement = timer.querySelector('[data-type="days"]');
            const hoursElement = timer.querySelector('[data-type="hours"]');
            const minutesElement = timer.querySelector('[data-type="minutes"]');
            const secondsElement = timer.querySelector('[data-type="seconds"]');
            
            if (daysElement) daysElement.textContent = days.toString().padStart(2, '0');
            if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
            if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
            if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, '0');
        });
    }
    
    // Update immediately
    updateCountdown();
    
    // Update every second
    const interval = setInterval(updateCountdown, 1000);
    
    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        clearInterval(interval);
    });
});
</script>
@endpush
