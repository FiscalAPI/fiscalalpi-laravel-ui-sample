{{-- resources/views/people/index.blade.php --}}
<x-layout.app-layout title="Personas">
    <x-layout.main-content
        title="Personas"
        subtitle="Aquí puedes ver y gestionar todas las personas registradas en el sistema."
    >
        <x-slot name="headerActions">
            <a href="{{ route('people.create') }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Agregar Persona
            </a>
        </x-slot>

        <div class="mt-8 flow-root">
            <div class="overflow-hidden bg-white shadow-sm border border-gray-200 sm:rounded-lg">
                <!-- Tabla con scroll vertical -->
                <div class="overflow-y-auto max-h-96 sm:max-h-[600px] lg:max-h-[700px]">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 bg-gray-50">
                                    Razón Social
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 bg-gray-50">
                                    Email
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 bg-gray-50">
                                    RFC
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 bg-gray-50">
                                    Régimen Fiscal
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 bg-gray-50">
                                    Uso CFDI
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 bg-gray-50">
                                    ID Fiscal API
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 bg-gray-50">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($people as $person)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <div class="max-w-xs">
                                            <div class="font-medium text-gray-900">{{ Str::limit($person->legalName, 40) }}</div>
                                            @if(strlen($person->legalName) > 40)
                                                <div class="text-xs text-gray-500 mt-1" title="{{ $person->legalName }}">
                                                    {{ Str::limit($person->legalName, 60) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                        <span class="font-medium">{{ $person->email }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                        @if($person->tin)
                                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $person->tin }}</span>
                                        @else
                                            <span class="text-gray-400 italic">No asignado</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                        @if($person->satTaxRegime)
                                            <div class="font-medium text-gray-900">{{ $person->satTaxRegime->code }}</div>
                                            <div class="text-xs text-gray-600">{{ Str::limit($person->satTaxRegime->description, 25) }}</div>
                                        @else
                                            <span class="text-gray-500">{{ $person->satTaxRegimeId ?: 'No asignado' }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                        @if($person->satCfdiUse)
                                            <div class="font-medium text-gray-900">{{ $person->satCfdiUse->code }}</div>
                                            <div class="text-xs text-gray-600">{{ Str::limit($person->satCfdiUse->description, 25) }}</div>
                                        @else
                                            <span class="text-gray-500">{{ $person->satCfdiUseId ?: 'No asignado' }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                        @if($person->fiscalapiId)
                                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $person->fiscalapiId }}</span>
                                        @else
                                            <span class="text-gray-400 italic">No asignado</span>
                                        @endif
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex items-center justify-end gap-x-3">
                                            <a href="{{ route('people.show', $person) }}"
                                               class="text-indigo-600 hover:text-indigo-900 font-medium hover:underline">
                                                Ver
                                            </a>
                                            <a href="{{ route('people.edit', $person) }}"
                                               class="text-blue-600 hover:text-blue-900 font-medium hover:underline">
                                                Editar
                                            </a>
                                            <form action="{{ route('people.destroy', $person) }}" method="POST"
                                                  onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta persona?');"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900 font-medium hover:underline">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3 py-8 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay personas</h3>
                                            <p class="mt-1 text-sm text-gray-500">Comienza creando tu primera persona.</p>
                                            <div class="mt-6">
                                                <a href="{{ route('people.create') }}"
                                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                    </svg>
                                                    Crear Persona
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Información de conteo -->
                @if($people->count() > 0)
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="text-sm text-gray-700">
                            <span class="font-medium">{{ $people->count() }}</span>
                            {{ Str::plural('persona', $people->count()) }} registrada{{ Str::plural('s', $people->count()) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </x-layout.main-content>
</x-layout.app-layout>
