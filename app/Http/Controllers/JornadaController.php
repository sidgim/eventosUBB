<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Jornada;

class JornadaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jornada = Jornada::all()->load('evento');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'colaboradores' => $jornada
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
                    'nombreJornada' => 'required'
                ]
            );

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'faltan datos de la jornada'
                ];
            } else {
                //guardar datos
                $jornada = new Jornada();
                $jornada->nombreJornada = $params_array['nombreJornada'];
                $jornada->fechaJornada = $params_array['fechaJornada'];
                $jornada->horaInicioJornada = $params_array['horaInicioJornada'];
                $jornada->horaFinJornada = $params_array['horaFinJornada'];
                $jornada->ubicacionJornada = $params_array['ubicacionJornada'];
                $jornada->descripcionJornada = $params_array['descripcionJornada'];
                $jornada->evento_idEvento = $params_array['evento_idEvento'];
                $jornada->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'se guardaron los datos de la jornada correctamente'
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
        $jornada = Jornada::where('evento_idEvento', $id)->get()->load('evento');

        if (is_object($jornada)) {
            $data =
                [
                    'code' => 200,
                    'status' => 'success',
                    'jornadas' => $jornada
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

     //metodo para retornar un solo expositor
     public function showJornadaById($id){

        $jornada = Jornada::find($id);
    
        if (is_object($jornada)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'jornada' => $jornada
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La jornada no existe'
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
                'nombreJornada' => 'required'
            ]);

            if ($validate->fails()) {

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error al actualizar datos'
                ];
            } else {


                $jornada = Jornada::where('idJornada', $id)->update($params_array);

                $data = [
                    'code' => 200,
                    'status' => 'succes',
                    'jornadas' => $jornada
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
        $jornada = Jornada::find($id);
        if (!empty($jornada)) {
            $jornada->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'jornadas' => $jornada
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El colaborador que desea borrar no existe'
            ];
        }
        return response()->json($data);
    }
}
