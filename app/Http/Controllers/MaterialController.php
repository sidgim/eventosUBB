<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Material;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $material = Colaborador::all()->load('evento');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'colaboradores' => $material
        ], 200);
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
            $validate = \Validator::make(
                $params_array,
                [
                    'nombreMaterial' => 'required',
                    'evento_idEvento' => 'required'
                ]
            );

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'faltan datos del material',
                    'errors' => $validate->errors()
                ];
            } else {
                //guardar datos
                $material = new Material();
                $material->nombreMaterial = $params_array['nombreMaterial'];
                $material->archivo = $params_array['archivo'];
                $material->evento_idEvento = $params_array['evento_idEvento'];

                $material->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'se guardaron los datos del material correctamente'
                ];
            }
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Error con los datos'
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
        $material = Material::where('evento_idEvento',$id)->get();

        if ($material != null) {
            $data =
                [
                    'code' => 200,
                    'status' => 'success',
                    'material' => $material
                ];
        } else {
            $data =
                [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'La entrada no existe'
                ];
        }
        return response()->json($data);
    }

     //metodo para retornar un solo material
     public function showMaterialById($id){
        $material = Material::find($id);
        if (is_object($material)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'material' => $material
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'el material no existe'
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
                'nombreMaterial' => 'required',
                'evento_idEvento' => 'required'
            ]);

            if ($validate->fails()) {

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error al actualizar datos'
                ];
            } else {
                unset($params_array[$id]);

                $material = Material::where('idMaterial', $id)->update($params_array);

                $data = [
                    'code' => 200,
                    'status' => 'succes',
                    'material' => $material
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
    public function destroy($id)
    {
        $material = Material::find($id);
        if (!empty($material)) {
            $material->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'colaborador' => $material
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El material que desea borrar no existe'
            ];
        }


        return response()->json($data);
    }



    public function upload(Request $request)
    {
        // Recoger datos de la petición
        $image = $request->file('file0');
        // Validar archivo

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|mimes:pptx,pdf,txt,xlsx'
        ]);

        //Guardar imagen 
        if (!$image || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir material'
            ];
        } else {
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('material')->put($image_name, \File::get($image));

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
        $isset = \Storage::disk('material')->exists($filename);

        if ($isset) {
            $file = \Storage::disk('material')->get($filename);
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

    public function downloadMaterial($filename)
    {
        //este es el original:
        // $file_path = public_path('../storage/app/material/'.$filename);

        $file_path = storage_path('app/material/'.$filename);
        //return response()->json($data);

        return response()->download($file_path);
        
        // return response()->$file_path;
        // prueba cami: 
        // $headers = array('Content-Type: application/pdf'); 
        //echo $file_path;
        //return Storage::download($file_path);
        //response()->file($file_path);

        //prueba cami 3: 
        // $uploaded = Storage::get($file_path);
        // return (new \Illuminate\Http\Response($uploaded))->header('Content-Type','application/pdf');


        //parte del prueba cami:
        //return response()->json($filename);


    }

   
}
