@component('mail::message')
# Recuperar tu contraseña

Hola {{ $user->name }},

Recibimos una solicitud para recuperar tu contraseña en {{ config('app.name') }}. Para continuar, por favor ingresa el siguiente código de verificación:

@component('mail::panel')
<div style="font-family: 'Courier New', monospace; font-size: 32px; font-weight: bold; letter-spacing: 8px; text-align: center; padding: 20px; background-color: #f4f6fb; border-radius: 8px; color: #171717;">
    {{ $otp }}
</div>
@endcomponent

Este código es válido por **10 minutos** y solo puede usarse una vez.

Si no solicitaste recuperar tu contraseña, puedes ignorar este correo de forma segura. Tu cuenta permanecerá segura.

Saludos,
{{ config('app.name') }}
@endcomponent