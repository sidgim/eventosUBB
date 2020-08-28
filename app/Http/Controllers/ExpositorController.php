<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Expositor;
use App\Actividad;
use App\Actividad_Expositor;

class ExpositorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expositor = Expositor::all();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'expositor' => $expositor
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
            $validate = \Validator::make($params_array, [

                'nombreExpositor' => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado el expositor.',
                    'errors' => $validate->errors()
                ];
            } else {
                $expositor = new Expositor();
                $expositor->nombreExpositor  = $params_array['nombreExpositor'];
                $expositor->apellidoExpositor  = $params_array['apellidoExpositor'];
                $expositor->sexo = $params_array['sexo'];
                $expositor->correoExpositor  = $params_array['correoExpositor'];
                $expositor->empresa  = $params_array['empresa'];
                $expositor->foto  = $params_array['foto'];
                $expositor->telefonoExpositor = $params_array['telefonoExpositor'];
                $expositor->apellido2Expositor = $params_array['apellido2Expositor'];
                $expositor->save();

                $acti_expo = new Actividad_Expositor();
                $acti_expo->evento = $params_array['evento'];
                $acti_expo->expositor_idExpositor = $expositor['idExpositor'];
                $acti_expo->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'expositor' => $expositor
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se envio ningun expositor'
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
        
        $expositor = Actividad_Expositor::where('evento','=',$id)->get()->load('expositor', 'actividad');

        if (is_object($expositor)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'expositor' => $expositor
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El expositor no existe'
            ];
        }

        return response()->json($data);
    }
 
    //metodo para retornar un solo expositor
    public function showExpositorById($id){
        $expositor = Expositor::find($id);
        if (is_object($expositor)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'expositor' => $expositor
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El expositor no existe'
            ];
        }

        return response()->json($data);
    }

    public function showExpositor($id)
    {
        
        $expo = Actividad_Expositor::where('evento',$id)->get();
        $expositor =array();
        foreach($expo as $expositores){
            if($expositores['expositor_idExpositor']!=null){
                $exposito = Expositor::where('idExpositor', $expositores['expositor_idExpositor'])->first();
                if(!in_array($exposito,$expositor)){
                    $expositor [] = $exposito;   
                

                }
            }
           
        }
     
        //$expositor = Expositor::where('idExpositor','=', $valor);

        if ($expositor!=null) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'expositor' => $expositor
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El expositor no existe'
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
                'nombreExpositor' => 'required'
            ]);

            if ($validate->fails()) {

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'Error al actualizar datos',
                    'errors' => $validate->errors()

                ];
            } else {
                unset($params_array[$id]);

                $expositor = Expositor::where('idExpositor', $id)->update($params_array);

                $data = [
                    'code' => 200,
                    'status' => 'succes',
                    'expositor' => $params_array
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
        $expositor = Expositor::find($id);
        if (!empty($expositor)) {
            $expositor->delete();

            $data = [
                'code' => 200,
                'status' => 'success',
                'expositor' => $expositor
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El expositor que desea borrar no existe'
            ];
        }


        return response()->json($data);
    }
    public function expositorActividad ($id){
        $actExpositor = Actividad_Expositor::where('evento', $id)->get()->load(['expositor','actividad']);
        $data = [
        'code' => 200,
        'status' => 'success',
        'expositor' => $actExpositor
    ];
    return response()->json($data);
    }
}
