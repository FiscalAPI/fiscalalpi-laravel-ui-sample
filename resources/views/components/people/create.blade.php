{{-- resources/views/people/create.blade.php --}}
<x-layout.app-layout title="Crear Persona">
    <x-layout.main-content
        title="Crear Nueva Persona"
        subtitle="Completa el formulario para registrar una nueva persona en el sistema."
        :show-breadcrumbs="true"
        :breadcrumbs="[
            ['name' => 'Personas', 'href' => route('people.index')],
            ['name' => 'Crear']
        ]"
    >
        <form action="{{ route('people.store') }}" method="POST">
            @include('people._form')
        </form>
    </x-layout.main-content>
</x-layout.app-layout>
