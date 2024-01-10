<?php

return [
    'tools' => [
        'header' => [
            'text' => 'string',
            'level' => [
                'type' => 'int',
                'required' => false,
            ],
        ],
        'quote' => [
            'text' => 'string',
            'caption' => 'string',
            'alignment' => 'string',
        ],
        'image' => [
            'file' => [
                'type' => 'array',
                'data' => [
                    'url' => 'string',
                ],
            ],
            'caption' => 'string',
            'withBorder' => 'boolean',
            'stretched' => 'boolean',
            'withBackground' => 'boolean',
        ],
        'paragraph' => [
            'text' => [
                'type' => 'string',
                'allowedTags' => 'i,b,u,a[href]',
            ],
        ],
        'codeBox' => [
            'code' => 'string',
            'language' => 'string',
            'theme' => 'string'
        ],
        'delimiter' => [
        ],
        'list' => [
            'style' => 'string',
            'items' => [
                'type' => 'array',
                'data' => [
                    '-' => [
                        'type' => 'string',
                        'allowedTags' => 'i,b,u,a[href]',
                    ],
                ],
            ],

        ],
    ],
];
