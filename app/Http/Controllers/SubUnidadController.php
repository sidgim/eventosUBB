<?php

namespace App\Http\Controllers;

use App\Mail\AgregarUnidad;
use App\Users_unidad;
use App\User;
use App\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubUnidadController extends Controller
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
    public function store(Request $request)
    {
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        if (!empty($params_array)) {
            $validate = \Validator::make($params_array, [

                'nombreUnidad' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado la unidad.',
                    'errors' => $validate->errors()
                ];
            } else {

                //Buscamos en la tabla intermedia al encargado principal de la Unidad
                $admin = Users_unidad::where('users_id', $params_array['idAdminUnidad'])
                    ->where('perfilUnidad', '3')->first();

                //Buscamos la unidad a la que pertenece el encargado
                $unidad = Unidad::where('idUnidad', $admin['unidad_idUnidad'])->first();

                //Buscamos a la persona que queremos dejar como subUnidad y le asignamos el 
                //un nuevo perfil que es 5(subUnidad) dentro del sistema
                $user = User::where('email', $params_array['email'])->first();
                if ($user['perfil_idPerfil']== 6 || $user['perfil_idPerfil']== 2 || 
                    $user['perfil_idPerfil']== 4 || $user['perfil_idPerfil'] == 5) {
                    $user->perfil_idPerfil = 5;
                    $user->save();
                    //creamos en la tabla intermedia los datos de la subUnidad 
                    $userUnidad = new Users_unidad();
                    $userUnidad->users_id = $user['id'];
                    $userUnidad->unidad_idUnidad = $unidad['idUnidad'];
                    $userUnidad->perfilUnidad = 4;
                    $userUnidad->save();
                    Mail::to($params_array['email'])->queue(new AgregarUnidad($user));
                    $data = [
                        'code' => 200,
                        'status' => 'success',
                        'usuario' => $user,
                        'unidad' => $unidad
                ];
                }else {
                    $data = [
                        'code' => 400,
                        'status' => 'error'
                    ]; 
                }
                
                
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se envio ningun dato'
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
        //Buscamos en la tabla intermedia al encargado principal de la Unidad
        $admin = Users_unidad::where('users_id', $id)
            ->where('perfilUnidad', '3')->first();

        //Buscamos la unidad a la que pertenece el encargado
        $unidad = Unidad::where('idUnidad', $admin['unidad_idUnidad'])->first();

        $subUnidad = Users_unidad::where('unidad_idUnidad', $unidad['idUnidad'])->where('perfilUnidad', '4')->get()->load('user', 'unidad');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'subUnidad' => $subUnidad
        ], 200);
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
                'users_id' => 'required',
                'unidad_idUnidad' => 'required'
            ]);

            if ($validate->fails()) {

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error al actualizar datos'
                ];
            } else {
                unset($params_array[$id]);

                //busco y actualizo a la persona que este a cargo de esa unidad
                $subUnidad = Users_unidad::where('idusers_unidad',$id)->update($params_array);

                $data = [
                    'code' => 200,
                    'status' => 'succes',
                    'subUnidad' => $subUnidad
                ];
            }
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

     //metodo en comun elimina a un adminUnidad o a un subUnidad y le cambia el perfil
     //esto lo hace uno a uno mediante el id la tabla intermedia
    public function destroy($id)
    {
        $subUnidad = Users_unidad::where('idusers_unidad',$id)->first();
        if (!empty($subUnidad)) {  
            $subUnidad->delete();  
            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $subUnidad
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El evento que desea borrar no existe'
            ];
        }


        return response()->json($data);
    }
}
