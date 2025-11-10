@component('mail::message')
# ¡Gracias por suscribirte, {{ $user->name }}!

Tu plan **{{ $plan->name }}** quedó activo. Mientras la suscripción esté vigente podrás descargar cualquier plantilla del catálogo sin límites.

@component('mail::panel')
**Plan:** {{ $plan->name }}
**Precio:** €{{ number_format($plan->price, 2) }} {{ strtoupper($plan->currency ?? 'EUR') }}
**Próxima renovación:** {{ optional($subscription->current_period_end)->format('d/m/Y H:i') ?? 'Automática' }}
@endcomponent

Ingresa al portal cuando quieras para empezar a descargar.

@component('mail::button', ['url' => config('app.frontend_url')])
Ir al portal
@endcomponent

Si necesitas cancelar o realizar cambios, puedes gestionar la suscripción desde tu perfil.

Saludos,
{{ config('app.name') }}
@endcomponent