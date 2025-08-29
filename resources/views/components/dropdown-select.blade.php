{{-- resources/views/components/dropdown-select.blade.php --}}
@props([
    'name',
    'label',
    'options' => [],
    'selected' => null,
    'required' => false,
    'placeholder' => 'Selecciona una opción...'
])

<div {{ $attributes->merge(['class' => 'sm:col-span-3']) }}>
    <label for="{{ $name }}" class="block text-sm/6 font-medium text-gray-900">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <div class="mt-2">
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            @if($required) required @endif
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
        >
            <option value="">{{ $placeholder }}</option>
            @foreach($options as $option)
                <option
                    value="{{ $option->code }}"
                    @if($selected == $option->code) selected @endif
                >
                    {{ $option->code }} - {{ $option->description }}
                </option>
            @endforeach
        </select>
    </div>
    @error($name)
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
