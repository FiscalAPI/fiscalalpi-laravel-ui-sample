{{-- resources/views/products/_form.blade.php --}}
@csrf

<div class="space-y-12">
    <div class="border-b border-gray-900/10 pb-12">
        <h2 class="text-base/7 font-semibold text-gray-900">Información del Producto</h2>
        <p class="mt-1 text-sm/6 text-gray-600">Proporciona los detalles del producto y selecciona los códigos SAT correspondientes.</p>

        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
            {{-- Campo Descripción --}}
            <div class="col-span-full">
                <label for="description" class="block text-sm/6 font-medium text-gray-900">
                    Descripción <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <textarea id="description" name="description" rows="3" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Precio Unitario --}}
            <div class="sm:col-span-3">
                <label for="unitPrice" class="block text-sm/6 font-medium text-gray-900">
                    Precio Unitario <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <input id="unitPrice" type="number" name="unitPrice" step="0.000001" value="{{ old('unitPrice', $product->unitPrice ?? '') }}" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </div>
                @error('unitPrice')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Fiscal API ID --}}
            <div class="sm:col-span-3">
                <label for="fiscalapiId" class="block text-sm/6 font-medium text-gray-900">ID Fiscal API</label>
                <div class="mt-2">
                    <input id="fiscalapiId" type="text" name="fiscalapiId" value="{{ old('fiscalapiId', $product->fiscalapiId ?? '') }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                </div>
                @error('fiscalapiId')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dropdown SAT Unidad de Medida --}}
            <x-dropdown-select 
                name="sat_unit_measurement_id"
                label="Unidad de Medida SAT"
                :options="$satUnitMeasurements ?? []"
                :selected="old('sat_unit_measurement_id', $product->sat_unit_measurement_id ?? 'H87')"
                :required="true"
                placeholder="Selecciona una unidad de medida..."
            />

            {{-- Dropdown SAT Objeto de Impuesto --}}
            <x-dropdown-select 
                name="sat_tax_object_id"
                label="Objeto de Impuesto SAT"
                :options="$satTaxObjects ?? []"
                :selected="old('sat_tax_object_id', $product->sat_tax_object_id ?? '02')"
                :required="true"
                placeholder="Selecciona un objeto de impuesto..."
            />

            {{-- Dropdown SAT Código de Producto --}}
            <x-dropdown-select 
                name="sat_product_code_id"
                label="Código de Producto SAT"
                :options="$satProductCodes ?? []"
                :selected="old('sat_product_code_id', $product->sat_product_code_id ?? '01010101')"
                :required="true"
                placeholder="Selecciona un código de producto..."
                class="col-span-full"
            />
        </div>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-x-6">
    <a href="{{ route('products.index') }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        Guardar Producto
    </button>
</div>
