<x-mail::message>
# ¡Bienvenido a Baseball SaaS!

Tu cuenta de administrador para la liga ha sido creada exitosamente.

Aquí tienes tus credenciales de acceso:

- **Email:** {{ $user->email }}
- **Contraseña:** {{ $password }}

<x-mail::button :url="url('/app/login')">
Ir al Panel de Administración
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
