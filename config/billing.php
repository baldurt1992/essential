<?php

$appUrl = env('APP_URL', 'http://localhost');

return [
    'currency' => 'eur',

    'stripe' => [
        'success_url' => env('STRIPE_SUCCESS_URL', rtrim($appUrl, '/') . '/billing/success'),
        'cancel_url' => env('STRIPE_CANCEL_URL', rtrim($appUrl, '/') . '/billing/cancel'),
        'subscription_trial_days' => env('STRIPE_TRIAL_DAYS', 0),
    ],
];
