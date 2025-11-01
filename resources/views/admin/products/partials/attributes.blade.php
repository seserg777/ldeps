{{-- Product Attributes Tab --}}
<div class="attributes-section">
    <div class="mb-4 flex justify-between items-center">
        <h4 class="text-lg font-semibold">Product Attributes / Variations</h4>
        <button type="button" 
                @click="addAttribute()"
                class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded">
            <i class="fas fa-plus mr-2"></i>
            Add Variation
        </button>
    </div>
    
    @php
        $attributes = $product->productAttributes ?? collect();
        
        // Get available extra fields for attributes (attr_12 to attr_24)
        $attrFields = [];
        for ($i = 12; $i <= 24; $i++) {
            if ($i == 21) continue;
            $field = \App\Models\ProductExtraField::find($i);
            if ($field) {
                $attrFields[$i] = [
                    'field' => $field,
                    'values' => \App\Models\ProductExtraFieldValue::where('field_id', $i)->orderBy('ordering')->get()
                ];
            }
        }
    @endphp
    
    <div class="space-y-4" x-data="{ 
        attributes: {{ $attributes->toJson() }},
        addAttribute() {
            this.attributes.push({
                product_attr_id: null,
                price: 0,
                retail_price: 0,
                buy_price: 0,
                count: 0,
                ean: '',
                manufacturer_code: '',
                special_price: false,
                unit: 1,
                @foreach($attrFields as $id => $data)
                attr_{{ $id }}: null,
                @endforeach
            });
        },
        removeAttribute(index) {
            if (confirm('Remove this variation?')) {
                this.attributes.splice(index, 1);
            }
        }
    }">
        <template x-if="attributes.length === 0">
            <div class="text-center py-8 text-gray-500">
                <p>No attribute variations. Click "Add Variation" to create one.</p>
            </div>
        </template>
        
        <template x-for="(attr, index) in attributes" :key="index">
            <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="font-medium text-gray-900">Variation <span x-text="index + 1"></span></h5>
                    <button type="button" 
                            @click="removeAttribute(index)"
                            class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <input type="hidden" :name="'attributes[' + index + '][product_attr_id]'" x-model="attr.product_attr_id">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Prices --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ціна дилерська</label>
                        <input type="number" 
                               :name="'attributes[' + index + '][buy_price]'"
                               x-model="attr.buy_price"
                               step="0.01"
                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ціна роздрібна</label>
                        <input type="number" 
                               :name="'attributes[' + index + '][retail_price]'"
                               x-model="attr.retail_price"
                               step="0.01"
                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ціна базова</label>
                        <input type="number" 
                               :name="'attributes[' + index + '][price]'"
                               x-model="attr.price"
                               step="0.01"
                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Кількість</label>
                        <input type="number" 
                               :name="'attributes[' + index + '][count]'"
                               x-model="attr.count"
                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                    </div>
                </div>
                
                {{-- Attribute Fields (attr_12 to attr_24) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    @foreach($attrFields as $fieldId => $data)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $data['field']->name }}
                            </label>
                            <select :name="'attributes[' + index + '][attr_{{ $fieldId }}]'"
                                    x-model="attr.attr_{{ $fieldId }}"
                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                <option value="">---</option>
                                @foreach($data['values'] as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
                
                {{-- Additional fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">EAN</label>
                        <input type="text" 
                               :name="'attributes[' + index + '][ean]'"
                               x-model="attr.ean"
                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Код виробника</label>
                        <input type="text" 
                               :name="'attributes[' + index + '][manufacturer_code]'"
                               x-model="attr.manufacturer_code"
                               class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

