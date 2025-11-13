@component('mail::message')
# ¡Gracias por tu compra{{ $isGuest ? '' : ', ' . ($user->name ?? '') }}!

Tu plantilla **{{ $template->title }}** ya está disponible para descargar.

@if($isGuest)
@component('mail::panel')
**Descargas realizadas:** {{ $license->download_count }}
**Límite de descargas:** {{ $license->download_limit ?? 'Ilimitado' }}
**Válido hasta:** {{ $license->expires_at ? $license->expires_at->format('d/m/Y') : 'Sin límite' }}
@endcomponent

@component('mail::button', ['url' => $downloadUrl])
Descargar ahora
@endcomponent

Este enlace de descarga es temporal y expirará en 30 días. Si necesitas volver a descargarla, puedes solicitar un nuevo enlace desde la página de confirmación.
@else
@component('mail::panel')
**Código de compra:** {{ $purchaseCode ?? 'N/A' }}
**Descargas realizadas:** {{ $license->download_count }}
**Límite de descargas:** {{ $license->download_limit ?? 'Ilimitado' }}
@endcomponent

Para descargar tu plantilla, ingresa a tu panel de cliente y usa el código de compra proporcionado.
@endif

¡Que disfrutes la plantilla!

Saludos,
{{ config('app.name') }}
@endcomponent