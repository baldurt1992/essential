@component('mail::message')
# ¡Gracias por tu compra, {{ $user->name }}!

Tu plantilla **{{ $template->title }}** ya está disponible para descargar.

@component('mail::panel')
**Código de compra:** {{ $license->purchase_code }}
**Descargas realizadas:** {{ $license->download_count }}
**Límite de descargas:** {{ $license->download_limit ?? 'Ilimitado' }}
@endcomponent

@component('mail::button', ['url' => $downloadUrl])
Descargar ahora
@endcomponent

Si necesitas volver a descargarla en el futuro, podrás hacerlo desde tu panel o usando este mismo código.

¡Que disfrutes la plantilla!

Saludos,
{{ config('app.name') }}
@endcomponent