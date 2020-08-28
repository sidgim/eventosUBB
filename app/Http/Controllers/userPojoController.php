<?php

namespace App\Http\Controllers;

use App\Mail\AgregarUnidad;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Unidad;
use App\User;
use App\Users_unidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class userPojoController extends Controller
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
     
    }

   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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

                $unidad = Unidad::where('idUnidad', $id)->first();
                $unidad->nombreUnidad = $params_array['nombreUnidad'];
                $unidad->logoUnidad = $params_array['logo'];
                $unidad->sede = $params_array['sede'];
                $unidad->save();

                $user = User::where('email', $params_array['email'])->first();
                $user->unidad_idUnidad = $unidad['idUnidad'];
                $user->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'evento' => $user,
                    'Colaborador' => $unidad

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /** ---------Funciones propias de las predeterminadas  ----------- */

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
            \Storage::disk('unidad')->put($image_name, \File::get($image));

            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
        }

        //Devolver el resultado 
        return response()->json($data);
    }



    public function getImage($filename)
    {
        $isset = \Storage::disk('unidad')->exists($filename);

        if ($isset) {
            $file = \Storage::disk('unidad')->get($filename);
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
        $isset = \Storage::disk('unidad')->exists($filename);

        if ($isset) {
            $file = \Storage::disk('unidad')->get($filename);
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
