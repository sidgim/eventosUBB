<?php

namespace App\Http\Controllers;

use App\Actividad;
use App\Actividad_Expositor;
use App\Expositor;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $actividad = Actividad::all()->load('jornada');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'actividad' => $actividad,
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
                    'nombreActividad' => 'required',
                ]
            );

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'faltan datos de la actividad',
                    'errors' => $validate->errors(),
                ];
            } else {
                //guardar datos
                $actividad = Actividad::create($params_array);

                if ($params_array['expositor'] == null) {
                    $params_array['expositor_idExpositor'] = null;
                    $params_array['actividad_idActividad'] = $actividad['idActividad'];
                    $actividad_expositor = Actividad_Expositor::create($params_array);
                } else {
                    foreach ($params_array['expositor'] as $a) {
                        $act_exp = Actividad_Expositor::where('expositor_idExpositor', $a['idExpositor'])->where('evento', $params_array['evento'])->first();

                        if ($act_exp['actividad_idActividad'] == null) {
                            $act_exp->actividad_idActividad = $actividad['idActividad'];
                            $act_exp->save();
                        } else {
                            $actividad_expositor = new Actividad_Expositor();
                            $actividad_expositor->evento = $params_array['evento'];
                            $actividad_expositor->expositor_idExpositor = $a['idExpositor'];
                            $actividad_expositor->actividad_idActividad = $actividad['idActividad'];
                            $actividad_expositor->save();
                        }
                    }
                }
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'se guardaron los datos de la actividad correctamente',
                ];
            }
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'Error con los datos',
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

        $acti = Actividad_Expositor::where('evento', $id)->get();
        $actividad = array();
        foreach ($acti as $actividades) {
            $activi = Actividad::where('idActividad', $actividades['actividad_idActividad'])->first();
            if (!in_array($activi, $actividad)) {
                $actividad[] = $activi;
            }
        }
        if ($actividad != null) {
            $data =
                [
                'code' => 200,
                'status' => 'success',
                'actividades' => $actividad,
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

    //metodo para retornar una sola actividad
    public function showActividadById($id)
    {
        $actividad = Actividad::where('idActividad',$id)->get()->load('jornada');
        $expositor = Actividad_Expositor::where('actividad_idActividad', $id)->get()->load('expositor');
        if (is_object($actividad)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'actividad' => $actividad,
                'expositor' =>  $expositor,
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'la actividad no existe',
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
                'nombreActividad' => 'required',
                'jornada_idJornada' => 'required',

            ]);

            if ($validate->fails()) {

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error al actualizar datos',

                ];
            } else {
                unset($params_array[$id]);

                $actividad = Actividad::where('idActividad', $id)->update($params_array);

                $data = [
                    'code' => 200,
                    'status' => 'succes',
                    'actividad' => $actividad,
                ];
            }
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
        $actividad = Actividad::find($id);
        if (!empty($actividad)) {
            $actividad->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'actividad' => $actividad,
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La actividad que desea borrar no existe',
            ];
        }
        return response()->json($data);
    }
}
