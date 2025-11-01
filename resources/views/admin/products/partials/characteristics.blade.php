{{-- Product Characteristics Tab --}}
<div class="characteristics-section">
    <h4 class="text-lg font-semibold mb-4">Product Characteristics</h4>
    
    @php
        // Get all available extra fields
        $extraFields = \App\Models\ProductExtraField::orderBy('ordering')->get();
        
        // Get current product characteristics
        $currentCharacteristics = $product->productCharacteristics->keyBy('extra_field');
    @endphp
    
    @if($extraFields->count() > 0)
        <div class="space-y-6">
            @foreach($extraFields as $field)
                @php
                    // Get field values for this extra field
                    $fieldValues = \App\Models\ProductExtraFieldValue::where('field_id', $field->id)
                        ->orderBy('ordering')
                        ->get();
                    
                    // Get currently selected value
                    $currentValue = $currentCharacteristics->get($field->id);
                    $selectedValueId = $currentValue ? $currentValue->extra_field_value : null;
                @endphp
                
                <div class="characteristic-field">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $field->name }}
                    </label>
                    
                    @if($fieldValues->count() > 0)
                        <select name="characteristics[{{ $field->id }}]" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">---</option>
                            @foreach($fieldValues as $value)
                                <option value="{{ $value->id }}" 
                                        {{ $selectedValueId == $value->id ? 'selected' : '' }}>
                                    {{ $value->name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <p class="text-sm text-gray-500 italic">No values available for this field</p>
                    @endif
                    
                    @if($field->description)
                        <p class="mt-1 text-xs text-gray-500">{{ $field->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No characteristics defined yet</p>
    @endif
</div>

<style>
    .characteristic-field select {
        max-height: 200px;
    }
</style>

