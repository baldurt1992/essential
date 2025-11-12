@component('mail::message')
# Nuevo mensaje desde Essential

Recibimos un nuevo mensaje a través del formulario público.

@component('mail::panel')
- **Nombre:** {{ $messageData->name }}
- **Correo:** {{ $messageData->email }}
@if($messageData->phone)
- **Teléfono:** {{ $messageData->phone }}
@endif
@if($messageData->company)
- **Empresa / Marca:** {{ $messageData->company }}
@endif
@if($messageData->subject)
- **Interés:** {{ $messageData->subject }}
@endif
@if($messageData->originUrl)
- **Página de origen:** {{ $messageData->originUrl }}
@endif
@endcomponent

**Mensaje**

{{ $messageData->message }}

Gracias,<br>
Essential Studio
@endcomponent


