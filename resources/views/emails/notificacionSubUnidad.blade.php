@component('mail::message')
# Hola {{$user->nombreUsuario}},

Has sido agregado a la comisión de un evento en la página EventosUBB. Para comenzar a editar los datos del evento ingresa a la página y accede a la opción "Eventos a cargo" del menú lateral.

Saludos,<br>
{{ config('app.name') }}
@endcomponent