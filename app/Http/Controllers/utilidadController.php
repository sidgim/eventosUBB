<?php

namespace App\Http\Controllers;

use App\Utilidad;
use Illuminate\Http\Request;

class utilidadController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $utilidad = Utilidad::find($id);

        if (is_object($utilidad)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'ciudad' => $utilidad
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La utilidad no existe'
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
        //
    }

    public function upload(Request $request) {
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
    
    public function getImage($filename) {
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
}
