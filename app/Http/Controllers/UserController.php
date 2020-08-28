<?php

namespace App\Http\Controllers;

use App\Evento;
use App\Evento_users;
use App\Mail\CambioPass;
use App\Mail\UserCreated;
use App\Unidad;
use App\User;
use App\Users_unidad;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Recoger los datos del usuario por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $params = json_decode($json);

        if (!empty($params_array) && !empty($params)) {
            //Limpiar datos
            $params_array = array_map('trim', $params_array);

            //Validar datos
            $validate = \Validator::make($params_array, [
                'nombreUsuario' => 'required|regex:/^[\pL\s\-]+$/u',
                'apellidoUsuario' => 'required|alpha',
                'email' => 'required|email|unique:users',
                'password' => 'required',

            ]);

            if ($validate->fails()) {
                $data = [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario existe o tienes un campo sin llenar',
                    'errors' => $validate->errors(),
                ];
            } else if (Str::contains($params_array['email'], ['gmail.com', 'hotmail.com', 'outlook.com', 'outlook.cl', 'alumnos.ubiobio.cl']) == true) {

                //Cifrar la contraseña
                $pwd = hash('sha256', $params->password);

                //Crear el usuario

                $user = new User();
                $user['perfil_idPerfil'] = 2;
                $user->nombreUsuario = $params_array['nombreUsuario'];
                $user->apellidoUsuario = $params_array['apellidoUsuario'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->save();
                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario  se ha creado correctamente',
                ];
            } else if (Str::contains($params_array['email'], 'ubiobio.cl') == true) {
                //Cifrar la contraseña
                $pwd = hash('sha256', $params->password);

                //Crear el usuario
                $user = new User();
                $user['perfil_idPerfil'] = 4;
                $user->nombreUsuario = $params_array['nombreUsuario'];
                $user->apellidoUsuario = $params_array['apellidoUsuario'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->save();
                $data = [
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario  se ha creado correctamente',
                ];
            }
        } else {

            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviado no son correctos',
            ];
        }
        return response()->json($data);
    }

    public function google(Request $request)
    {
        $jwtAuth = new \JwtAuth();
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $client = new Google_Client([
            'client_id' => '964769714688-k37ooin32et4b2a7iokpbtcv2nn5ca41.apps.googleusercontent.com',
        ]);
        $payload = $client->verifyIdToken($params_array);

        $pwd = hash('sha256', $payload['sub']);
        if ($payload) {
            if (Str::contains($payload['email'], ['gmail.com', 'hotmail.com', 'outlook.com', 'outlook.cl', 'alumnos.ubiobio.cl']) == true) {

                $user = new User();
                $user['perfil_idPerfil'] = 2;
                $user['verified'] = 1;
                $user->google_id = $payload['sub'];
                $user->nombreUsuario = $payload['given_name'];
                $user->apellidoUsuario = $payload['family_name'];
                $user->password = $pwd;
                $user->email = $payload['email'];

                if (!User::where('email', $user->email)->first()) {
                    $user->save();
                }

                $signup = $jwtAuth->signup($payload['email'], $pwd, true);

            } else if (Str::contains($payload['email'], 'ubiobio.cl') == true) {
                $user = new User();
                $user['perfil_idPerfil'] = 4;
                $user['verified'] = 1;
                $user->google_id = $payload['sub'];
                $user->nombreUsuario = $payload['given_name'];
                $user->apellidoUsuario = $payload['family_name'];
                $user->password = $pwd;
                $user->email = $payload['email'];

                if (!User::where('email', $user->email)->first()) {
                    $user->save();
                }
                $signup = $jwtAuth->signup($payload['email'], $pwd, true);
            }

        } else {

            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviado no son correctos',
            ];
            return response()->json($data);

        }

        return response()->json($signup, 200);

    }

    public function login(Request $request)
    {
        $jwtAuth = new \JwtAuth();

        //Recibir post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        //Validar esos datos
        $validate = \Validator::make($params_array, [

            'email' => 'required|email',
            'password' => 'required',

        ]);

        if ($validate->fails()) {
            $data = [
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors(),
            ];

            return response()->json($data);

        } else {
            //Cifrar contraseña
            $pwd = hash('sha256', $params->password);
            //Devolver token o datos
            $signup = $jwtAuth->signup($params->email, $pwd);
            if (!empty($params->gettoken)) {
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }

        return response()->json($signup, 200);
    }

    public function update2(Request $request, $id)
    {
       // $token = $request->header('Authorization');
       // $jwtAuth = new \JwtAuth();

        //$checkToken = $jwtAuth->checkToken($token);
        //Recoger los datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

       

            //validar los datos
            $validate = \Validator::make($params_array, [
                'nombreUsuario' => 'required|alpha',
                'apellidoUsuario' => 'required|alpha',
                'email' => 'required|email|unique:users'
            ]);
            if ($validate->fails()) {
            //quitar los campos que no quiero actualizar de la peticion
            unset($params_array['id']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            unset($params_array['perfil_idPerfil']);

            //actualizar usuario en bbdd
            $user_update = User::where('id', $id)->update($params_array);

            //devolver un array con los resultados
            $user = User::where('id', $id)->get();
            $data = [
                'code' => 200,
                'status' => 'success',
                'user' => $user,
                'changes' => $params_array,
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no se encuentra identificado',
            ];
        }
        return response()->json($data);
    }

    public function upload(Request $request)
    {
        // Recoger datos de la petición
        $image = $request->file('file0');
        // Validar imagen

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif',
        ]);

        //Guardar imagen
        if (!$image || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen',
            ];
        } else {
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name,
            ];
        }

        //Devolver el resultado
        return response()->json($data);
    }

    //Crear unidad
    public function getAll($id)
    {
        $users = User::where('id', '!=', $id)->where('perfil_idPerfil', '!=', 1)->where('perfil_idPerfil', '!=', 2)->get()->load('perfil');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'users' => $users,
        ]);
    }

    public function getUsuariosEncargado()
    {
        $users = User::where('perfil_idPerfil', '!=', 1)->where('perfil_idPerfil', '!=', 2)->get()->load('perfil');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'users' => $users,
        ]);
    }

    public function getUsuarioComision($id)
    {
        $users = User::where('id', '!=', $id)->where('perfil_idPerfil', '!=', 1)->get()->load('perfil');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'users' => $users,
        ]);
    }

    public function getImage($filename)
    {
        $isset = \Storage::disk('users')->exists($filename);

        if ($isset) {
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No existe la imagen',
            ];
        }
        return response()->json($data);
    }

    public function detail($id)
    {
        $user = User::find($id);
        if (is_object($user)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'user' => $user,
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'el usuario no existe',
            ];
        }
        return response()->json($data);
    }

    public function verify($token)
    {
        $user = User::where('id', $token)->first();

        $user->verified = "1";

        $user->save();

        $data = [
            'code' => 200,
            'status' => 'succes',
            'message' => 'usuario identificado ',
        ];
        return redirect('http://parra.chillan.ubiobio.cl:8090/~gaston.lara1401/eventosUBB/');

    }

    public function resend(User $user)
    {
        if ($user->esVerificado()) {
            return $this->errorResponse('Este usuario ya ha sido verificado.', 409);
        }

        retry(5, function () use ($user) {
            Mail::to($user)->send(new UserCreated($user));
        }, 100);

        return $this->showMessage('El correo de verificación se ha reenviado');

    }

    //envio de correo para cambiar contraseña
    public function password(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $user = User::where('email', $params_array['email'])->first();
        if ($user) {
            Mail::to($params_array['email'])->send(new CambioPass($user));
            $data = [
                'code' => 200,
                'status' => 'success',
                'usuario' => $user,
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'failed',
                'usuario' => $user,
            ];
        }

        return response()->json($data);
    }

    //metodo para cambiar la contraseña
    public function cambiarContraseña(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $params = json_decode($json);

        $pwd = hash('sha256', $params->passNueva);

        $user = User::where('id', $params_array['id'])->first();
        if ($user) {
            $user->password = $pwd;

            $user->save();

            $data = [
                'code' => 200,
                'status' => 'succes',
                'user' => $user,
            ];
            //return redirect('http://parra.chillan.ubiobio.cl:8090/~gaston.lara1401/eventosUBB/');
            return response()->json($data);

        } else {
            $data = [
                'code' => 400,
                'status' => 'failed',
            ];
            return response()->json($data);

        }
    }

    //Retorna datos del usuario activo para obtener su perfil
    public function getDataUsuario($idUsuario)
    {
        $users = User::where('id', $idUsuario)->get()->load('perfil');

        if (is_object($users)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'user' => $users,

            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no existe',
            ];
        }

        return response()->json($data);

    }

    //reporte de admin ubb
    public function getReporteAdminUBB()
    {
        $evento = Evento::All();
        $reporte = array();
        //recorremos unidad por unidad
        foreach ($evento as $event) {
            $nombreUnidad = Unidad::where('nombreUnidad', $event['nombreEventoInterno'])->first();
            $userUnidad = Users_unidad::where('unidad_idUnidad', $nombreUnidad['idUnidad'])->first();
            $user = User::where('id', $userUnidad['users_id'])->first();
            $encargado = Evento_users::where('evento_idEvento', $event['idEvento'])->where('rol_idRol', 1)->first();
            $encargadoEvent = User::where('id', $encargado['users_id'])->first();
            $fechaEvent = Evento::where('idEvento', $event['idEvento'])->first()->load('jornada', 'ciudad');
            //if($userUnidad =! null){
            $data = [
                "nombreUnidad" => $nombreUnidad['nombreUnidad'],
                "encargadoUnidad" => $user['nombreUsuario'],
                "apellidoUnidad" => $user['apellidoUsuario'],
                "nombreEvento" => $event['nombreEvento'],
                "encargadoEvento" => $encargadoEvent['nombreUsuario'],
                "apellidoEncargado" => $encargadoEvent['apellidoUsuario'],
                "fechaEvento" => $fechaEvent,
                "cantidadParticipante" => $event['capacidad'],
                "ciudad" => $fechaEvent,

            ];
            //}
            $reporte[] = $data;
        }

        $dato = [
            'code' => 200,
            'status' => 'success',
            'reporte' => $reporte,
        ];

        return response()->json($dato);

    }

    //devuelve todos los datos necesarios para realizar el reporte del adminUnidad
    public function getReporteAdminUnidad(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $reporte = array();
        $data = array();

        $evento = Evento::where('nombreEventoInterno', $params_array)->get()->load('jornada', 'ciudad');
        foreach ($evento as $event) {
            //obtenemos el nombre del encargado
            $encargado = Evento_users::where("evento_idEvento", $event['idEvento'])->
                where('rol_idRol', 1)->first();
            $user = User::where('id', $encargado['users_id'])->first();
            //obtenemos todos los nombres de la comision y se almacenan en un arreglo
            $comision = Evento_users::where("evento_idEvento",$event['idEvento'])->
                where('rol_idRol', 3)->where('contadorEvento', 0)->get()->load('users');

                  
                
           
            //almacenamos todos los datos por evento
            $reporte[] = [
                "encargado" => $user,
                "evento" => $event,
                "comision" => $comision,
            ];
            //se van almacenando todos los eventos en posiciones distintas dentro de un arreglo
            //$reporte[] = $json;

        }
        $dato = [
            'code' => 200,
            'status' => 'success',
            'reporte' => $reporte,
        ];

        return response()->json($dato);

    }

}
