<?php

return [
    'default_password' => env('DEFAULT_PASSWORD', '123Eg33k!@#$'),
    'subfolder_domain' => env('SUBFOLDER_DOMAIN', '/'),
    'recaptcha_key' => [
        'secret' => env('RECAPTCHA_SECRET_KEY', false),
        'site' => env('RECAPTCHA_SITE_KEY', false),
    ],
];
