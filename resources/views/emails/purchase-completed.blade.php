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
**Descargas realizadas:** {{ $license->download_count }}
**Límite de descargas:** {{ $license->download_limit ?? 'Ilimitado' }}
@endcomponent

**Código de compra:**

<div style="background: #f4f6fb; border: 2px solid #dd3333; border-radius: 12px; padding: 24px; margin: 24px 0; text-align: center; font-family: 'Courier New', 'IBM Plex Mono', monospace; font-size: 28px; font-weight: 700; letter-spacing: 6px; color: #171717; cursor: text; user-select: all; -webkit-user-select: all; -moz-user-select: all; -ms-user-select: all;">
    {{ $purchaseCode ?? 'N/A' }}
</div>

<div style="text-align: center; margin: 20px 0;">
    <p style="font-size: 14px; color: #666; margin: 0 0 10px; font-weight: 500;">💡 Haz clic en el código de arriba para seleccionarlo y copiarlo fácilmente</p>
    <p style="font-size: 14px; color: #666; margin: 0;">Para descargar tu plantilla, ingresa a tu panel de cliente y usa el código de compra proporcionado.</p>
</div>
@endif

¡Que disfrutes la plantilla!

Saludos,
{{ config('app.name') }}
@endcomponent