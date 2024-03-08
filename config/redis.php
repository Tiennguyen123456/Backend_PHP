<?php

return [
    'event' => [
        'import' => [
            'duplicate_index' => 'event:%s:import:duplicate_index',
        ],
        'client' => [
            'total' => 'event:%s:client:total',
            'checkin' => 'event:%s:client:checkin',
        ]
    ],
    'campaign' => [
        'clients' => 'campaign:%s:clients',
        'mail_sents' => 'campaign:%s:mail_sents',
    ],
];
