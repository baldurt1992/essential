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

@component('mail::button', ['url' => config('app.url') . '/?verify_email=' . urlencode($user->email)])
Verificar mi correo ahora
@endcomponent

**¿No puedes ver la página de verificación?** Si recargaste o cerraste la página por error, haz clic en el botón de arriba para abrir el modal de verificación y completar tu registro.

Si no solicitaste este código, puedes ignorar este correo.

Saludos,
{{ config('app.name') }}
@endcomponent

