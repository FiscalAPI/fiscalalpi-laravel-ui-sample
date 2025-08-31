<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Layout Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the application layout components
    | including navigation, branding, and default values.
    |
    */

    'branding' => [
        'logo' => 'https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500',
        'alt_text' => 'Your Company',
        'company_name' => 'Your Company',
    ],

    'navigation' => [
        'items' => [
            [
                'name' => 'Dashboard',
                'href' => '/',
                'icon' => 'dashboard',
                'permission' => null,
            ],
            [
                'name' => 'Productos',
                'href' => '/products',
                'icon' => 'box',
                'permission' => null,
            ],
            [
                'name' => 'Personas',
                'href' => '/people',
                'icon' => 'team',
                'permission' => null,
            ],
            [
                'name' => 'Punto de Venta',
                'href' => '/pos',
                'icon' => 'pos',
                'permission' => null,
            ],
            [
                'name' => 'Ventas',
                'href' => '/sales',
                'icon' => 'sales',
                'permission' => null,
            ],
        ],
    ],

    'topbar' => [
        'default_search_placeholder' => 'Search',
        'show_search' => true,
        'show_notifications' => true,
        'search_action' => '#',
        'search_method' => 'GET',
    ],

    'profile' => [
        'default_user_name' => 'Jesus Mendoza',
        'default_user_avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
        'menu_items' => [
            [
                'label' => 'Your profile',
                'href' => '#',
                'icon' => null,
            ],
            [
                'label' => 'Sign out',
                'href' => '#',
                'icon' => null,
            ],
        ],
    ],
];
