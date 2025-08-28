<x-layout title="Configuración">
    <x-main-content title="Configuración" breadcrumb="Configuración del Sistema">
        <div class="max-w-2xl mx-auto space-y-6">
            <!-- Configuración General -->
            <div class="bg-white border rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Configuración General</h3>
                <form class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre de la App</label>
                            <input type="text" value="Mi Aplicación" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Zona Horaria</label>
                            <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option>America/Mexico_City</option>
                                <option>UTC</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Configuración de Notificaciones -->
            <div class="bg-white border rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Notificaciones</h3>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" checked class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Notificaciones por email</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Notificaciones push</span>
                    </label>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end space-x-3">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </x-main-content>
</x-layout>
