<x-layout title="Perfil">
    <x-main-content title="Mi Perfil" breadcrumb="Perfil de Usuario">
        <div class="max-w-md mx-auto">
            <div class="text-center mb-6">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4"></div>
                <h3 class="text-xl font-semibold">Usuario</h3>
                <p class="text-gray-600">usuario@example.com</p>
            </div>

            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                    Actualizar Perfil
                </button>
            </form>
        </div>
    </x-main-content>
</x-layout>
