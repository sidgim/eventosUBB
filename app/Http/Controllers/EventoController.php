<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Evento;


class EventoController extends Controller
{
    /*
    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index','show']]);
    }
      
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $eventos = Evento::where('visibilidad', 1)->get()->load('ciudad');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'eventos' => $eventos
        ]);
    }
    public function show($id)
    {

        $evento = Evento::find($id)->load('ciudad');

        if (is_object($evento)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $evento
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
                $evento->nombreEvento  = $params_array['nombreEvento'];
                $evento->ubicacion  = $params_array['ubicacion'];
                $evento->direccion = $params_array['direccion'];
                $evento->detalles  = $params_array['detalles'];
                $evento->imagen  = $params_array['imagen'];
                $evento->capacidad  = $params_array['capacidad'];
                $evento->nombreEventoInterno = $params_array['nombreEventoInterno'];
                $evento->ciudad_idCiudad = $params_array['ciudad_idCiudad']; 
                $evento->save();            

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
                'message' => 'No se envio ningun evento',
                'params' => $params_array
            ];
        }
        return response()->json($data);
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
                'nombreEvento' => 'required'
            ]);

            if ($validate->fails()) {

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error al actualizar datos'
                ];
            } else {
                unset($params_array[$id]);
                $evento = Evento::where('idEvento', $id)->update($params_array);
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
    public function destroy($id, Request $request)
    {
        $evento = Evento::find($id);
        if (!empty($evento)) {
            $evento->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'evento' => $evento
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

    public function upload(Request $request)
    {
        //Recoger la imagen de la petición 
        $image = $request->file('file0');
        //Validar la imagen 
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);
        //Guardar la imagen
        if (!$image || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            ];
        } else {
            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('images')->put($image_name, \File::get($image));

            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
        }
        //Devolver datos
        return response()->json($data);
    }

    public function getImage($filename)
    {
        //Comprobar si existe el fichero
        $image = \Storage::disk('images')->exists($filename);

        if ($image) {
            //Conseguir la imagen
            $file = \Storage::disk('images')->get($filename);
            //Devolver la imagen
            return new Response($file, 200);
        } else {
            //Mostrar error
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe'
            ];
            return response()->json($data);
        }
    }
}
