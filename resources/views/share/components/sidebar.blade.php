@props([
    'title' => 'Фільтри',
    'class' => 'col-lg-3 col-md-4',
    'sticky' => true
])

<div class="{{ $class }}">
    <div class="card {{ $sticky ? 'sticky-top' : '' }}" style="top: 100px;">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>{{ $title }}
            </h6>
        </div>
        <div class="card-body">
            {{ $slot }}
        </div>
    </div>
</div>
