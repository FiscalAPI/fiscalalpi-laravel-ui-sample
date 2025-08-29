<x-layout.app-layout title="User Profile">
    <x-layout.main-content
        title="John Doe"
        subtitle="User Profile Details"
        :show-breadcrumbs="true"
        :breadcrumbs="[
            ['name' => 'Dashboard', 'href' => '/'],
            ['name' => 'Users', 'href' => '/users'],
            ['name' => 'John Doe']
        ]"
        header-actions='
            <button type="button" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Edit
            </button>
            <button type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Save Changes
            </button>
        '>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">User Information</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Personal details and application.</p>
            </div>
            <div class="border-t border-gray-200">
                <!-- Contenido del perfil -->
            </div>
        </div>
    </x-layout.main-content>
</x-layout.app-layout>
