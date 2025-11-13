@component('mail::message')
# Verifica tu correo electrónico

Hola {{ $user->name }},

Gracias por registrarte en {{ config('app.name') }}. Para completar tu registro, por favor ingresa el siguiente código de verificación:

@component('mail::panel')
<div style="font-family: 'Courier New', monospace; font-size: 32px; font-weight: bold; letter-spacing: 8px; text-align: center; padding: 20px; background-color: #f4f6fb; border-radius: 8px; color: #171717;">
{{ $otp }}
</div>
@endcomponent

Este código es válido por **10 minutos** y solo puede usarse una vez.

Si no solicitaste este código, puedes ignorar este correo.

Saludos,
{{ config('app.name') }}
@endcomponent

