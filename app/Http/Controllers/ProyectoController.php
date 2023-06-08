<?php

namespace App\Http\Controllers;

use App\Models\AsignacionSeguimiento;
use App\Models\Proyecto;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ProyectoController extends Controller
{
    public $reglas = [
        'nombre' => 'required',
        'fecha_incial' => 'required|date',
        'fecha_final' => 'required|date|after:fecha_incial',
        'id_user_crear' => 'required|integer',
    ];

    public $mensajes = [
        'required' => 'El campo :attribute es obligatorio.',
        'date' => 'El campo :attribute debe ser una fecha válida.',
        'integer' => 'El campo :attribute debe ser un número entero.',
        'after' => 'El campo :attribute debe ser una fecha posterior a la fecha inicial.',
    ];

    public function index()
    {
        $proyectos = Proyecto::all(['id','nombre','fecha_incial','fecha_final','estado','id_user_crear']);

        foreach ($proyectos as $key => $value) {
            $seguimiento = AsignacionSeguimiento::where('proeycto_id','=',$value->id)->get(['tarea_id']);
            $tareas = [];
            foreach($seguimiento as $seguim){
                array_push($tareas,Tarea::leftJoin('users','users.id','=','tareas.responsable')
                                            ->select('tareas.id','descripcion','prioridad','fecha_limite','responsable','estado','users.name')
                                            ->findOrFail($seguim->tarea_id));
            }
            $value->tareas = $tareas;
        }

        return response()->json([
            'data' => $proyectos,
            "estado" => 0
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->post();
        $validator = Validator::make($data,$this->reglas,$this->mensajes);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json([
                'errors' => $errors,
                "estado" => 1
            ],  Response::HTTP_OK);
        }

        $proyecto = Proyecto::create($data);

        return response()->json([
            'data' =>  $proyecto,
            "estado" => 0
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Proyecto $proyecto)
    {
        return response()->json([
            'data' =>  $proyecto,
            "estado" => 0
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Proyecto $proyecto)
    {
        $data = $request->post();
        $validator = Validator::make($data,$this->reglas,$this->mensajes);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json([
                'errors' => $errors,
                "estado" => 1
            ],  Response::HTTP_OK);
        }

        $proyecto->update($data);

        return response()->json([
            'data' =>  $proyecto,
            "estado" => 0
        ], Response::HTTP_CREATED);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Proyecto $proyecto)
    {
        $proyecto->delete();
        return response()->json([
            'data' =>  'Proyecto Eliminada',
            "estado" => 0
        ], Response::HTTP_OK);
    }
}
