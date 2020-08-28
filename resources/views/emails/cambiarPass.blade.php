@component('mail::message')
# Hola {{$user->nombreUsuario}},
Notamos que has olvidado tu contraseña en la página EventosUBB, para cambiar tu contraseña presiona el siguiente botón.

@component('mail::button', ['url' => 'http://parra.chillan.ubiobio.cl:8090/~gaston.lara1401/eventosUBB/#/cambioPass/'.$user->id])
Cambiar Contraseña
@endcomponent

Gracias,<br>
{{ config('app.name') }}
@endcomponent