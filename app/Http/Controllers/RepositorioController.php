<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Repositorio;

class RepositorioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $eventos = Repositorio::all()->load('evento');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'eventos' => $eventos
        ]);
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
                    'archivo' => 'required',
                    'evento_idevento' => 'required'
                ]
            );

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'error, no se ha detectado identificador del evento',
                    'errors' => $validate->errors()
                ];
            } else {
                //guardar datos
                $repositorio = new Repositorio();
                $repositorio->archivo = $params_array['archivo'];
                $repositorio->evento_idevento = $params_array['evento_idevento'];

                $repositorio->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'se guardaron los datos del repositorio correctamente'
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
        $repositorio = Repositorio::where('evento_idevento',$id)->get();

        if ($repositorio != null) {
            $data =
                [
                    'code' => 200,
                    'status' => 'success',
                    'repositorio' => $repositorio
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $repositorio= Repositorio::find($id);
        if (!empty($repositorio)) {
            $repositorio->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $repositorio,
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El repositorio no fue borrado exitosamente',
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
            'file0' => 'required|mimes:pptx,pdf,txt,jpg,png,xlsx,docx'
        ]);

        //Guardar imagen 
        if (!$image || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir archivo'
            ];
        } else {
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('repositorio')->put($image_name, \File::get($image));

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
        $isset = \Storage::disk('repositorio')->exists($filename);

        if ($isset) {
            $file = \Storage::disk('repositorio')->get($filename);
            return new Response($file, 200);
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'No existe el archivo'
            ];
            return response()->json($data);
        }
        
    }

    public function downloadRepositorio($filename)
    {
        $file_path = storage_path('app/repositorio/'.$filename);
        return response()->download($file_path);
    }
}
