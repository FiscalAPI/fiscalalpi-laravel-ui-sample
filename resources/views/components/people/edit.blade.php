{{-- resources/views/people/edit.blade.php --}}
<x-layout.app-layout title="Editar Persona">
    <x-layout.main-content
        title="Editar Persona"
        subtitle="Actualiza la información de la persona '{{ $person->legalName }}'."
        :show-breadcrumbs="true"
        :breadcrumbs="[
            ['name' => 'Personas', 'href' => route('people.index')],
            ['name' => 'Editar']
        ]"
    >
        <form action="{{ route('people.update', $person) }}" method="POST">
            @method('PUT')
            @include('people._form', ['person' => $person])
        </form>
    </x-layout.main-content>
</x-layout.app-layout>
