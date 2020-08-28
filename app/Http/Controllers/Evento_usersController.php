<?php

namespace App\Http\Controllers;

use App\Evento;
use App\Evento_users;
use App\Helpers\JwtAuth;
use App\Mail\AgregarSubUnidad;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class Evento_usersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store2(Request $request, $id)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            $val = Evento_users::where('users_id', $id)->where('evento_idEvento', $params_array['evento_idEvento'])->first();

            $validate = \Validator::make($params_array, [
                'evento_idEvento' => 'required',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado el evento.',
                    'errors' => $validate->errors(),
                ];
            } else {
                if ($params_array['evento_idEvento'] != $val['evento_idEvento']) {
                    $eventoU = new Evento_users();
                    $eventoU->contadorEvento = $params_array['contadorEvento'];
                    $eventoU->evento_idEvento = $params_array['evento_idEvento'];
                    $eventoU->rol_idRol = $params_array['rol_idRol'] = 2;
                    $eventoU->asistencia = 0;
                    $eventoU->users_id = $id;
                    $eventoU->save();
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'evento' => $eventoU,
                        'validacion' => $val,
                    ];
                } else {
                    $data = [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'ya se encuentra participando',

                    ];
                }

            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se envio ningun evento',
                'params' => $params_array,
            ];
        }
        return response()->json($data);
    }

    public function comision(Request $request, $id)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $cont = 0;
        if (!empty($params_array)) {

            foreach ($params_array as $evento) {
                $cont++;
                //usuario
                $idUser = User::where('email', $evento['email'])->first();
                $val = Evento_users::where('users_id', $idUser['id'])->where('evento_idEvento', $id)
                ->where('deleted_at', null)->first();
                // echo($val->rol_idRol);
                if ($val == null ) {

                    $eventoU = new Evento_users();
                    $eventoU->contadorEvento = 0;
                    $eventoU->evento_idEvento = $id;
                    $eventoU->rol_idRol = 3;
                    $eventoU->asistencia = 0;
                    $eventoU->users_id = $idUser['id'];
                    $eventoU->save();
                    Mail::to($idUser['email'])->queue(new AgregarSubUnidad($idUser));

                    if($idUser->perfil_idPerfil!=4 &&
                    $idUser->perfil_idPerfil!=5 &&
                    $idUser->perfil_idPerfil!=3 &&
                    $idUser->perfil_idPerfil!=6){
                        $idUser->perfil_idPerfil = 6;
                        $idUser->save();
                    }

                    
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'evento' => $eventoU,

                    ];
                }else if($cont == 1){
                    
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'El usuario ya pertenece a la comision',

                    ];
                }

            }

        }

        return response()->json($data);
    }

    //Elimina a un integrante de la comisión
    public function destroyComision(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $evento = Evento_users::where('evento_idEvento', $params_array['evento_idEvento'])
            ->where('users_id', $params_array['users_id']);
        if (!empty($evento)) {
            $evento->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $evento,
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El evento que desea borrar no existe',
            ];
        }

        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $evento = Evento_users::where('evento_idEvento', '=', $id)->where('rol_idRol', '=', 2)->get()->load('users');
        if (is_object($evento)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $evento,

            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El evento no existe',
            ];
        }

        return response()->json($data);
    }

    //para saber
    public function show2($id, $idUser)
    {
        $evento = Evento_users::where('evento_idEvento', '=', $id)->where('rol_idRol', '=', 3)->Orwhere('rol_idRol', '=', 1)
            ->where('users_id', $idUser)->get();
        if (is_object($evento)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $evento,

            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El evento no existe',
            ];
        }

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            $jwtAuth = new JwtAuth();
            $token = $request->header('Authorization', null);
            $user = $jwtAuth->checkToken($token, true);

            unset($params_array[$id]);
            $eventoU = Evento_users::where('evento_idEvento', $id);
            $eventoU->contadorEvento = $eventoU['contadorEvento'] - 1;
            $eventoU->evento_idEvento = $id;
            $eventoU->rol_idRol = $params_array['rol_idRol'];
            $eventoU->users_id = $user->sub;
            $eventoU->save();
            $data = [
                'code' => 200,
                'status' => 'succes',
                'evento' => $params_array,
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'datos no actualizados',
            ];
        }
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $evento = Evento_users::find($id);
        if (!empty($evento)) {
            $evento->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $evento,
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El evento que desea borrar no existe',
            ];
        }

        return response()->json($data);
    }
    public function getEventosByUser($id)
    {

        $eventos = Evento_users::where('users_id', '=', $id)->where('rol_idRol', '=', 2)->get()->load('evento');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'eventos' => $eventos,
        ]);
    }

    //Metodo para crear comision
    public function getEventosByAdmin($id)
    {

        $eventos = Evento_users::where('users_id', $id)->where('rol_idRol', '=', 1)->get();
        $evento = array();
        foreach ($eventos as $actividades) {
            $activi = Evento::where('idEvento', $actividades['evento_idEvento'])->first();
            if (!in_array($activi, $evento)) {
                $evento[] = $activi;
            }
        }
        if ($evento != null) {
            $data =
                [
                'code' => 200,
                'status' => 'success',
                'eventos' => $evento,
            ];
        } else {
            $data =
                [
                'code' => 404,
                'status' => 'error',
                'message' => 'La entrada no existe',
            ];
        }
        return response()->json($data);
    }

    public function getEventosByAdmin2($id)
    {

        $eventos = Evento_users::where('users_id', $id)->where('rol_idRol', '=', 1)->get();
        $eventos1 = Evento_users::where('users_id', $id)->where('rol_idRol', '=', 3)->get();
        $evento = array();
        foreach ($eventos as $actividades) {
            $activi = Evento::where('idEvento', $actividades['evento_idEvento'])->first();
            if (!in_array($activi, $evento) && $activi!=null) {
                $evento[] = $activi;
            }
        }

        foreach ($eventos1 as $actividades) {
            $activi1 = Evento::where('idEvento', $actividades['evento_idEvento'])->first();
            if (!in_array($activi1, $evento)  && $activi1!=null) {
                $evento[] = $activi1;
            }
        }
        if ($evento != null) {
            $data =
                [
                'code' => 200,
                'status' => 'success',
                'eventos' => $evento,
            ];
        } else {
            $data =
                [
                'code' => 404,
                'status' => 'error',
                'message' => 'La entrada no existe',
            ];
        }
        return response()->json($data);
    }

    public function getAllComision($id)
    {
        $comisiones = Evento_users::where('rol_idRol', '=', 3)->where('evento_idEvento', $id)->get()->load('users');

        return response()->json([
            'code' => 200,
            'status' => 'sucess',
            'comisiones' => $comisiones,
        ]);

    }

    public function getAllUsuariosEventoById($id){
        $evento = Evento_users::where('evento_idEvento', $id)->get()->load('users');

        if (is_object($evento)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $evento,

            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El evento no existe',
            ];
        }

        return response()->json($data);

    }

}
