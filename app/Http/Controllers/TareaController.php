<?php

namespace App\Http\Controllers;

use App\Models\AsignacionSeguimiento;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class TareaController extends Controller
{
    public $reglas = [
        'descripcion' => 'required',
        'prioridad' => 'required|string',
        'fecha_limite' => 'required|date',
    ];

    public $mensajes = [
        'required' => 'El campo :attribute es obligatorio.',
        'date' => 'El campo :attribute debe ser una fecha válida.',
        'integer' => 'El campo :attribute debe ser un número entero.',
        'string' => 'El campo :attribute debe ser una cadena de caracteres.',
    ];


    public function index($id)
    {
        $tareas = Tarea::join('asignacion_seguimientos', 'tareas.id', '=', 'asignacion_seguimientos.tarea_id')
            ->leftJoin('users', 'users.id', '=', 'tareas.responsable')
            ->where('asignacion_seguimientos.proeycto_id', '=', $id)
            ->select('tareas.id', 'descripcion', 'prioridad', 'fecha_limite', 'responsable', 'estado', 'users.name')
            ->get();

        $users = User::all();

        return response()->json([
            'data' => $tareas,
            'usuarios' => $users,
            "estado" => 0
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
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

        $tarea = Tarea::create($data);

        $seguimiento = new AsignacionSeguimiento();
        $seguimiento->proeycto_id = $id;
        $seguimiento->tarea_id = $tarea->id;
        $seguimiento->save();

        return response()->json([
            'data' =>  $tarea,
            "estado" => 0
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Tarea $tarea)
    {
        return response()->json([
            'data' =>  $tarea,
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
    public function update(Request $request, Tarea $tarea)
    {
        $data = $request->post();

        $validate = Validator::make($data, $this->reglas, $this->mensajes);

        if ($validate->fails()) {
            $errors = $validate->errors()->all();
            return response()->json(
                [
                    "errors" => $errors,
                    "estado" => 1
                ],
                Response::HTTP_OK
            );
        }
        $tarea->update($data);

        return response()->json([
            'data' =>  $tarea,
            "estado" => 0
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tarea $tarea)
    {
        AsignacionSeguimiento::where('tarea_id', '=', $tarea->id)->delete();
        $tarea->delete();
        return response()->json([
            'data' =>  'Tarea Eliminada',
            "estado" => 0
        ], Response::HTTP_OK);
    }


    public function subirArchivos(Request $request)
    {

        return response()->json([
            'data' =>  $request,
            "estado" => 0
        ], Response::HTTP_OK);
    }
}
