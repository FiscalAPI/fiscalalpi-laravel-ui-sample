<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-black/10 px-6 pb-4">
    <div class="flex h-16 shrink-0 items-center">
        <img src="{{ $branding['logo'] ?? 'https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500' }}"
             alt="{{ $branding['alt_text'] ?? 'Your Company' }}"
             class="h-8 w-auto" />
    </div>

    <x-layout.sidebar-navigation :items="$navigationItems" />
</div>
