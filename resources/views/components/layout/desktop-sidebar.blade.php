<div class="hidden bg-gray-900 ring-1 ring-white/10 lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-black/10 px-6 pb-4">
        <div class="flex h-16 shrink-0 items-center">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" class="h-8 w-auto" />
        </div>

        <x-layout.sidebar-navigation :items="$navigationItems" />
    </div>
</div>
