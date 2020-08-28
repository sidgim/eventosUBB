<?php

namespace App\Http\Controllers;

use App\Unidad;
use App\User;
use App\Users_unidad;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\AgregarUnidad;

class UnidadController extends Controller
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
    
     //Metodo que crea una unidad y asigna a un encargado de esa unidad
     public function store(Request $request)
     {
         $json = $request->input('json', null);
         $params_array = json_decode($json, true);
         //quita espacios y deja en minuscula
        //$str = mb_convert_case(trim($params_array['nombreUnidad']), MB_CASE_LOWER, "UTF-8");
         if (!empty($params_array)) {
             $validate = \Validator::make($params_array, [
 
                 'nombreUnidad' => 'required|unique:unidad'
             ]);
 
             if ($validate->fails()) {
                 $data = [
                     'code' => 400,
                     'status' => 'error',
                     'message' => 'No se ha guardado la unidad.',
                     'errors' => $validate->errors()
                 ];
             } else {
 
                 $unidad = new Unidad();
                 $unidad->nombreUnidad = $params_array['nombreUnidad'];
                 $unidad->logoUnidad = $params_array['logoUnidad'];
                 $unidad->sede = $params_array['sede'];
                 $unidad->save();
 
                 $user = User::where('email', $params_array['email'])->first();
                 $user->perfil_idPerfil = 3;
                 $user->save();
 
                 $userUnidad = new Users_unidad();
                 $userUnidad->users_id = $user['id'];
                 $userUnidad->unidad_idUnidad = $unidad['idUnidad'];
                 $userUnidad->perfilUnidad = 3;
                 $userUnidad->save();
 
                 $users = User::findOrFail($user['id']);
                 Mail::to($params_array['email'])->queue(new AgregarUnidad($users));
 
 
                 $data = [
                     'code' => 200,
                     'status' => 'success',
                     'usuario' => $user,
                     'unidad' => $unidad
 
                 ];
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
        $unidad = Users_unidad::where('users_id', '=', $id)->get()->load('Unidad','User');
        
        return response()->json([
			'code' => 200,
			'status' => 'success',
			'unidad' => $unidad,
		]);
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

            $validate = \Validator::make($params_array,[
                'nombreUnidad' => 'required'
            ]);

            if ($validate->fails()) {

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error al actualizar datos'
                ];
            } else {
                unset($params_array[$id]);
                $evento = Unidad::where('idUnidad', $id)->update($params_array);
                $data = [
                    'code' => 200,
                    'status' => 'succes',
                    'evento' => $params_array
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

    //Metodo que elimina una unidad y a todos los que pertenecen a esa unidad 
    public function destroy($id)
    {
        $Unidad = Unidad::where('idUnidad',$id)->first();
        $subUnidad = Users_unidad::where('unidad_idUnidad', $id)->get();
       
        if (!empty($Unidad)) {
            $Unidad->delete();
            //Eliminamos a todos los usuarios que estén en la unidad borrada
           // $subUnidad->delete();
            //recorremos a todos los que se encuentren en la tabla intermedia users_unidad y
            //por cada uno le cambiamos los permisos a encargado 
            foreach($subUnidad as $asignar){
                //buscamos al usuario 
                $user = User::where('id', $asignar['users_id'])->first();
                //cambiamos perfil
                $user->perfil_idPerfil = 4;
                $user->save();
            }

          
            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $Unidad
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La unidad no se encuentra'
            ];
        }


        return response()->json($data);
    
    }

	public function getAllUnidad() {
		$unidades = Users_unidad::where('unidad_idUnidad', '!=', null)->where('perfilUnidad', 3)->get()->load('Unidad','User');

		return response()->json([
			'code' => 200,
			'status' => 'success',
			'unidades' => $unidades,
		]);
    }

    //Función que reotorna todas las unidades sin arreglos anidados (para crear el evento)
    public function getAllUnidad2() {
        $unidades = Unidad::all();
        
        return response()->json([
            'code' => 200,
            'status' => 'success',
            'unidades' => $unidades,
        ]);
    }
    
    public function getUnidadById($id){

        
        //$unidad = Users_unidad::where('idusers_unidad', $id)->where('perfilUnidad', 3)->first()->load('Unidad');
        $unidad = Unidad::where('idUnidad', $id)->first();

        if(is_object($unidad)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'unidad' => $unidad
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'la unidad no existe'
            ];
        }

        return response()->json($data);
    }
    
}
