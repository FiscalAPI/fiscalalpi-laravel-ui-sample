{{-- resources/views/products/create.blade.php --}}
<x-layout.app-layout title="Crear Producto">
    <x-layout.main-content
        title="Crear Nuevo Producto"
        subtitle="Completa el formulario para registrar un nuevo producto en el sistema."
        :show-breadcrumbs="true"
        :breadcrumbs="[
            ['name' => 'Productos', 'href' => route('products.index')],
            ['name' => 'Crear']
        ]"
    >
        <form action="{{ route('products.store') }}" method="POST">
            @include('products._form')
        </form>
    </x-layout.main-content>
</x-layout.app-layout>
