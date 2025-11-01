{{-- Product Attributes Tab (Table View) --}}
<div class="attributes-section" x-data="{ 
    attributes: {{ $product->productAttributes->toJson() }},
    addAttribute() {
        this.attributes.push({
            product_attr_id: null,
            price: '',
            retail_price: '',
            buy_price: '',
            count: '',
            ean: '',
            manufacturer_code: '',
            special_price: false,
            unit: 1,
            @php
                for ($i = 12; $i <= 24; $i++) {
                    if ($i == 21) continue;
                    echo "attr_{$i}: null,\n";
                }
            @endphp
        });
    },
    removeAttribute(index) {
        if (confirm('Видалити цю варіацію?')) {
            this.attributes.splice(index, 1);
        }
    }
}">
    <div class="mb-4 flex justify-between items-center">
        <h4 class="text-lg font-semibold">Варіації товару</h4>
        <button type="button" 
                @click="addAttribute()"
                class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded">
            <i class="fas fa-plus mr-2"></i>
            Додати варіацію
        </button>
    </div>
    
    @php
        // Get available extra fields for attributes
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
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Варіація</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Ціна дилерська</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Ціна роздрібна</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Ціна базова</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Спецціна</th>
                    @foreach($attrFields as $fieldId => $data)
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">
                            {{ $data['field']->name }}
                        </th>
                    @endforeach
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Кількість</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">EAN</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Код</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-700 uppercase">Дії</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-if="attributes.length === 0">
                    <tr>
                        <td colspan="{{ 9 + count($attrFields) }}" class="px-3 py-8 text-center text-gray-500">
                            Немає варіацій. Натисніть "Додати варіацію" щоб створити.
                        </td>
                    </tr>
                </template>
                
                <template x-for="(attr, index) in attributes" :key="index">
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-sm">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg">🇺🇦</span>
                                <span x-text="'#' + (index + 1)"></span>
                            </div>
                            <input type="hidden" :name="'attributes[' + index + '][product_attr_id]'" x-model="attr.product_attr_id">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" 
                                   :name="'attributes[' + index + '][buy_price]'"
                                   x-model="attr.buy_price"
                                   step="0.01"
                                   placeholder="0.00"
                                   class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" 
                                   :name="'attributes[' + index + '][retail_price]'"
                                   x-model="attr.retail_price"
                                   step="0.01"
                                   placeholder="0.00"
                                   class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                        </td>
                        <td class="px-3 py-2">
                            <input type="number" 
                                   :name="'attributes[' + index + '][price]'"
                                   x-model="attr.price"
                                   step="0.01"
                                   placeholder="0.00"
                                   class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                        </td>
                        <td class="px-3 py-2 text-center">
                            <input type="checkbox" 
                                   :name="'attributes[' + index + '][special_price]'"
                                   x-model="attr.special_price"
                                   value="1"
                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </td>
                        @foreach($attrFields as $fieldId => $data)
                            <td class="px-3 py-2">
                                <select :name="'attributes[' + index + '][attr_{{ $fieldId }}]'"
                                        x-model="attr.attr_{{ $fieldId }}"
                                        class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                                    <option value="">---</option>
                                    @foreach($data['values'] as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        @endforeach
                        <td class="px-3 py-2">
                            <input type="number" 
                                   :name="'attributes[' + index + '][count]'"
                                   x-model="attr.count"
                                   placeholder="0"
                                   class="w-20 px-2 py-1 border border-gray-300 rounded text-sm">
                        </td>
                        <td class="px-3 py-2">
                            <input type="text" 
                                   :name="'attributes[' + index + '][ean]'"
                                   x-model="attr.ean"
                                   placeholder="EAN"
                                   class="w-32 px-2 py-1 border border-gray-300 rounded text-sm">
                        </td>
                        <td class="px-3 py-2">
                            <input type="text" 
                                   :name="'attributes[' + index + '][manufacturer_code]'"
                                   x-model="attr.manufacturer_code"
                                   placeholder="Код"
                                   class="w-24 px-2 py-1 border border-gray-300 rounded text-sm">
                        </td>
                        <td class="px-3 py-2 text-center">
                            <button type="button" 
                                    @click="removeAttribute(index)"
                                    class="text-red-600 hover:text-red-800"
                                    title="Видалити">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    
    <template x-if="attributes.length === 0">
        <div class="mt-4 text-center py-8 bg-gray-50 rounded border-2 border-dashed border-gray-300">
            <i class="fas fa-cube text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Немає варіацій товару</p>
            <button type="button" 
                    @click="addAttribute()"
                    class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded">
                <i class="fas fa-plus mr-2"></i>
                Додати першу варіацію
            </button>
        </div>
    </template>
</div>
