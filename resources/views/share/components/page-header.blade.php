@props([
    'title' => '',
    'subtitle' => '',
    'image' => null,
    'class' => 'bg-primary text-white py-5',
    'showBreadcrumbs' => true
])

<div class="{{ $class }}">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                @if($showBreadcrumbs)
                    <x-breadcrumbs :items="$breadcrumbs ?? []" class="bg-transparent py-0" />
                @endif
                
                <h1 class="display-5 fw-bold mb-3">
                    @if(isset($icon))
                        <i class="{{ $icon }} me-3"></i>
                    @endif
                    {{ $title }}
                </h1>
                
                @if($subtitle)
                    <p class="lead mb-0">{{ $subtitle }}</p>
                @endif
                
                @if(isset($actions))
                    <div class="mt-4">
                        {{ $actions }}
                    </div>
                @endif
            </div>
            
            @if($image)
                <div class="col-lg-4 text-center">
                    <img src="{{ $image }}" alt="{{ $title }}" class="img-fluid rounded">
                </div>
            @endif
        </div>
    </div>
</div>
