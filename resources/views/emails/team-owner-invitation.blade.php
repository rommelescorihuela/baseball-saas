<x-mail::message>
# Invitación a administrar el equipo {{ $team->name }}

Hola {{ $user->name }},

Has sido designado como encargado del equipo **{{ $team->name }}** en la liga **{{ $team->league->name }}**.

@if($password)
Se ha creado una cuenta para ti. Aquí tienes tus credenciales:

- **Email:** {{ $user->email }}
- **Contraseña:** {{ $password }}
@else
Puedes acceder a tu cuenta existente para administrar el equipo.
@endif

<x-mail::button :url="url('/app/login')">
Ir al Panel de Administración
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
