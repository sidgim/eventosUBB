<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Evento;
use App\Colaborador;
use App\Jornada;
use App\Expositor;
use App\Actividad;
use App\Actividad_Expositor;
use App\Material;
use App\User;
use App\Evento_users;
use App\Users_unidad;
use App\Helpers\JwtAuth;
use App\Mail\AgregarEncargado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EventoPojoController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

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

    //Metodo que crea un Evento y Asigna a un Encargado
    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);


        if (!empty($params_array)) {


            $validate = \Validator::make($params_array, [

                'nombreEventoInterno' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado el evento.',
                    'errors' => $validate->errors()
                ];
            } else {

                $evento = new Evento();
                $evento->visibilidad = 0;
                $evento->nombreEventoInterno = $params_array['nombreEventoInterno'];
                
                $evento->save();

                $eventoU = new Evento_users();
                $idUser = User::where('email', '=', $params_array['email'])->first();
                $eventoU->contadorEvento  = 0;
                $eventoU->evento_idEvento  = $evento['idEvento'];
                $eventoU->rol_idRol = 1;
                $eventoU->users_id  = $idUser['id'];
                $eventoU->save();

                $unidad = Users_unidad::where('users_id', $params_array['id'])->where('perfilUnidad', 3)->first();
                $userUnidad = new Users_unidad();
                $userUnidad->users_id = $eventoU['users_id'];
                $userUnidad->unidad_idUnidad = $unidad['unidad_idUnidad'];
                $userUnidad->perfilUnidad = 4;
                $userUnidad->save();

                // $user = User::findOrFail($idUser['id']);
                // Mail::to($params_array['email'])->send(new AgregarEncargado($user));

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'evento' => $evento
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se envio ningun evento'
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
        $evento = Evento::find($id)->load('ciudad');
        $material = Material::where('evento_idEvento', '=', $id)->get();
        $colaborador = Colaborador::where('evento_idEvento', '=', $id)->get();

        $jornada = Jornada::where('evento_idEvento', '=', $id)->get();
        /*  $jornada = array();
        foreach($jornadas as $jornad){
            if(!in_array($jornad, $jornada)){
                $jornada [] = $jornad;   
            }
        }
*/
        $acti = Actividad_Expositor::where('evento', $id)->get();
        $actividad = array();
        foreach ($acti as $actividades) {
            $activi = Actividad::where('idActividad', $actividades['actividad_idActividad'])->first();
            if (!in_array($activi, $actividad)) {
                $actividad[] = $activi;
            }
        }

        $expo = Actividad_Expositor::where('evento', $id)->get();
        $expositor = array();
        foreach ($expo as $expositores) {
            if ($expositores['expositor_idExpositor'] != null) {
                $exposito = Expositor::where('idExpositor', $expositores['expositor_idExpositor'])->first();
                if (!in_array($exposito, $expositor)) {
                    $expositor[] = $exposito;
                }
            }
        }

        if (is_object($evento)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $evento,
                'material' => $material,
                'colaborador' => $colaborador,
                'Jornada' => $jornada,
                'actividad' => $actividad,
                'expositor' => $expositor
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El evento no existe'
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


            $validate = \Validator::make($params_array, [
                'nombreEvento' => 'required'
            ]);

            if ($validate->fails()) {

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error al actualizar datos'
                ];
            } else {

                unset($params_array['idEvento']);
                $evento = Evento::where('idEvento', $id)->first();
                $evento->nombreEvento  = $params_array['nombreEvento'];
                $evento->ubicacion  = $params_array['ubicacion'];
                $evento->direccion = $params_array['direccion'];
                $evento->detalles  = $params_array['detalles'];
                $evento->imagen  = $params_array['imagen'];
                $evento->capacidad  = $params_array['capacidad'];
                $evento->visibilidad = $params_array['visibilidad'];
                $evento->nombreEventoInterno = $params_array['nombreEventoInterno'];
                $evento->ciudad_idCiudad = $params_array['ciudad_idCiudad'];
                $evento->categoria_idCategoria = $params_array['categoria_idCategoria'];
                $evento->tipoEvento_idtipoEvento = $params_array['tipoEvento_idtipoEvento'];
                $evento->save();

                /*
                $eventoU = Evento_users::where('evento_idEvento', $id)->where('users_id', $idUsuario)->first();
                $eventoU->contadorEvento  = 0;
                $eventoU->evento_idEvento  = $evento['idEvento'];
                $eventoU->users_id  = $idUsuario;
                $eventoU->save();*/
            }


            $data = [
                'code' => 200,
                'status' => 'succes',
                'evento' => $evento

            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'datos no actualizados'
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
    { }

    public function asistencia(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $evento = Evento_users::where('evento_idEvento', $params_array['evento_idEvento'])->where('users_id', $params_array['users_id'])->first();

        if ($evento->asistencia == '1') {

            $evento->asistencia = '0';
            $evento->save();

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'no se encuentra participando'
            ];
        } else {
            $evento->asistencia = '1';
            $evento->save();
            $data = [
                'code' => 200,
                'status' => 'succes',
                'message' => 'se encuentra participando'
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
            'file0' => 'required|file'
        ]);

        //Guardar imagen 
        if (!$image || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen'
            ];
        } else {
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('archivos')->put($image_name, \File::get($image));

            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
        }

        //Devolver el resultado 
        return response()->json($data);
    }

    public function showEventos(){
        $evento = Evento::where('visibilidad',1)->get()->load('jornada');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'eventosJornada' => $evento
        ]);
    }

    public function getFile($filename)
    {
        $isset = \Storage::disk('archivos')->exists($filename);

        if ($isset) {
            $file = \Storage::disk('archivos')->get($filename);
            return new Response($file, 200);
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No existe la imagen'
            ];
        }
        return response()->json($data);
    }

    public function downloadImage($filename)
    {
        $isset = \Storage::disk('archivos')->exists($filename);

        if ($isset) {
            $file = \Storage::disk('archivos')->get($filename);
            return response()->download($file);
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No existe la imagen'
            ];
            return response()->json($data);
        }
    }
}
