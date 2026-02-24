<x-mail::message>
# Has sido agregado al equipo {{ $team->name }}

Hola {{ $user->name }},

Has sido agregado como miembro del equipo **{{ $team->name }}** en nuestra plataforma de gestión de béisbol.

## Tus credenciales de acceso

**Correo electrónico:** {{ $user->email }}  
@if($password)
**Contraseña temporal:** `{{ $password }}`
@endif

## ¿Qué puedes hacer?

Dependiendo de tu rol asignado, podrás:

- **Secretaría:** Gestionar jugadores, documentos y rosters del equipo.
- **Coach:** Registrar estadísticas de partidos y eventos de juego.

<x-mail::button :url="config('app.url') . '/app/' . $team->league->slug">
Acceder al Panel
</x-mail::button>

Si tienes alguna pregunta, contacta al administrador de tu equipo.

Gracias por usar nuestra plataforma.

<x-mail::subcopy>
Si no esperabas este correo, puedes ignorarlo.
</x-mail::subcopy>
</x-mail::message>
