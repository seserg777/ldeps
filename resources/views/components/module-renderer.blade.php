@foreach($modules as $module)
    @php
        // Determine module type and corresponding view
        $moduleType = $module->module ?: 'custom';
        $viewPath = "components.modules.{$moduleType}";
        
        // Fallback to custom module if specific view doesn't exist
        if (!view()->exists($viewPath)) {
            $viewPath = 'components.modules.custom';
        }
    @endphp
    
    <div class="module module-{{ $module->id }} module-type-{{ $moduleType }} module-position-{{ $position }}" 
         data-module-id="{{ $module->id }}"
         data-module-type="{{ $moduleType }}">
        
        @if($module->showtitle && $module->title)
            <div class="module-title">
                <h3>{{ $module->title }}</h3>
            </div>
        @endif
        
        <div class="module-content">
            @include($viewPath, ['module' => $module])
        </div>
    </div>
@endforeach

