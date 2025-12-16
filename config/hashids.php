<?php

return [
    'default' => 'main',
    'connections' => [
        'main' => [
            'salt' => '',
            'length' => 10,
        ],
        'alternative' => [
            'salt' => env('HASHIDS_SALT', 'your-unique-app-salt-here'),
            'length' => 10,
        ],
    ],
]; 