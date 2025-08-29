{{-- resources/views/people/show.blade.php --}}
<x-layout.app-layout title="Detalle de la Persona">
    <x-layout.main-content
        title="Detalle de la Persona"
        subtitle="Detalles completos de la persona registrada con información de catálogos SAT."
        :show-breadcrumbs="true"
        :breadcrumbs="[
            ['name' => 'Personas', 'href' => route('people.index')],
            ['name' => 'Ver Detalles']
        ]"
    >
        <x-slot name="headerActions">
            <a href="{{ route('people.edit', $person) }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Editar Persona
            </a>
        </x-slot>

        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
            <div class="border-t border-gray-200">
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Razón Social</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $person->legalName }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $person->email }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">RFC</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $person->tin ?: 'N/A' }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Código Postal</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $person->zipCode ?: 'N/A' }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Régimen de Capital</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $person->capitalRegime ?: 'N/A' }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Régimen Fiscal SAT</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            @if($person->satTaxRegime)
                                <div class="font-medium">{{ $person->satTaxRegime->code }}</div>
                                <div class="text-gray-600">{{ $person->satTaxRegime->description }}</div>
                            @else
                                {{ $person->satTaxRegimeId ?: 'N/A' }}
                            @endif
                        </dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Uso CFDI SAT</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            @if($person->satCfdiUse)
                                <div class="font-medium">{{ $person->satCfdiUse->code }}</div>
                                <div class="text-gray-600">{{ $person->satCfdiUse->description }}</div>
                            @else
                                {{ $person->satCfdiUseId ?: 'N/A' }}
                            @endif
                        </dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">ID Fiscal API</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $person->fiscalapiId ?: 'N/A' }}</dd>
                    </div>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Fecha de Creación</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $person->created_at->format('d/m/Y H:i A') }}</dd>
                    </div>
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $person->updated_at->format('d/m/Y H:i A') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('people.index') }}" class="text-sm/6 font-semibold text-indigo-600 hover:text-indigo-500">
                ← Volver a la lista de personas
            </a>
        </div>

    </x-layout.main-content>
</x-layout.app-layout>
