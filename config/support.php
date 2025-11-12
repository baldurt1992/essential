<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Correo de respaldo para mensajes de contacto
    |--------------------------------------------------------------------------
    |
    | Si no existe un correo configurado en la información pública del sitio,
    | se utilizará este valor como fallback antes de recurrir al remitente por
    | defecto de la aplicación.
    |
    */
    'contact_fallback_email' => env('SUPPORT_CONTACT_EMAIL'),
];


