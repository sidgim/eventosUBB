@component('mail::message')
# Hola {{$user->nombreUsuario}},

Gracias por crear una cuenta en EventosUBB. Por favor verifícala usando el siguiente botón:

@component('mail::button', ['url' => 'http://parra.chillan.ubiobio.cl:8090/~gaston.lara1401/eventosUBB-laravel/public/api/verify/'.$user->id])
Confirmar mi cuenta
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent