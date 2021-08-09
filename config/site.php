<?php
return array(
    'site_info' =>
        array(
            'title' => 'PonePaste',
            'description' => 'PonePaste can store green',
            'baseurl' => 'ponepaste.local/',
            'keywords' => '',
            'site_name' => 'PonePaste',
            'email' => '',
            'google_analytics' => '',
            'additional_scripts' => 'PonePaste',
        ),
    'interface' =>
        array(
            'language' => 'en',
            'theme' => 'bulma',
        ),
    'permissions' => [
        'disable_guest' => false,
        'private' => false
    ],
    'mail' => [
        'verification' => false,
        'smtp_host' => '',
        'smtp_port' => '',
        'smtp_user' => '',
        'socket' => '',
        'auth' => '',
        'protocol' => ''
    ],
    'captcha' => [
        'enabled' => true,
        'multiple' => false,
        'mode' => 'Normal',
        'allowed' => 'ABCDEFGHIJKLMNOPQRSTUVYXYZabcdefghijklmnopqrstuvwxyz0123456789',
        'colour' => '#000000'
    ]
);