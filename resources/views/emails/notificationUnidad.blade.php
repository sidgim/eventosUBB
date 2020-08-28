@component('mail::message')
# Hola {{$user->nombreUsuario}},

Has sido asignado a una unidad en la página EventosUBB.

Saludos,<br>
{{ config('app.name') }}
@endcomponent