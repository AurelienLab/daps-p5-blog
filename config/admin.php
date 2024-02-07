<?php

return [
    'menu' => [
        [
            'name' => 'Dashboard',
            'icon' => 'home',
            'route' => 'admin.dashboard'
        ],
        [
            'name' => 'Articles',
            'icon' => 'newspaper',
            'route' => 'admin.post.index'
        ],
        [
            'name' => 'Catégories',
            'icon' => 'tag',
            'route' => 'admin.category.index'
        ],
        [
            'name' => 'Utilisateurs',
            'icon' => 'user-group',
            'route' => 'admin.user.index'
        ]
    ]
];
