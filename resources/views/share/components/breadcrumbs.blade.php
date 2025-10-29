@props([
    'items' => [],
    'homeText' => 'Home',
    'homeUrl' => null,
    'separator' => '/',
    'class' => 'bg-light py-3'
])

@php
    $homeUrl = $homeUrl ?? route('products.index');
@endphp

@if(!empty($items))
    <nav aria-label="breadcrumb" class="{{ $class }}">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <!-- Home -->
                <li class="breadcrumb-item">
                    <a href="{{ $homeUrl }}" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>{{ $homeText }}
                    </a>
                </li>
                
                <!-- Breadcrumb Items -->
                @foreach($items as $index => $item)
                    @if($index === count($items) - 1)
                        <!-- Last item (current page) -->
                        <li class="breadcrumb-item active" aria-current="page">
                            @if(isset($item['icon']))
                                <i class="{{ $item['icon'] }} me-1"></i>
                            @endif
                            {{ $item['title'] }}
                        </li>
                    @else
                        <!-- Regular item -->
                        <li class="breadcrumb-item">
                            <a href="{{ $item['url'] }}" class="text-decoration-none">
                                @if(isset($item['icon']))
                                    <i class="{{ $item['icon'] }} me-1"></i>
                                @endif
                                {{ $item['title'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </div>
    </nav>
@endif
