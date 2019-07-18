<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'route' => 'admin',
    'controllers' => [

    ],
    'locales' => [
        'en' => 'English',
    ],
    'login' => [
        'username'  => 'email',
        'title'     => 'Email',
        'recaptcha' => true
    ],
    'recaptcha' => [
        'site_key'        => env('RECAPTCHA_SITE_KEY'),
        'secret_key'      => env('RECAPTCHA_SECRET_KEY'),
        'ignore_on_debug' => true
    ],
    'theme' => [
        'footer_note' => '&copy; <script>document.write(new Date().getFullYear())</script>,
                        made with <i class="material-icons">favorite</i> by Creative Tim.
                        Powered by Sanjab',
        'footer_links' => [
            ['title' => 'CREATIVE TIM', 'link' => 'https://www.creative-tim.com/'],
            ['title' => 'SANJAB', 'link' => 'https://amir9480.github.io/'],
        ]
    ]
];
