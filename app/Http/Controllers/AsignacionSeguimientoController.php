<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use App\Models\Proyecto;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsignacionSeguimientoController extends Controller
{
    public function metricas()
    {
        $datos = DB::select("SELECT users.id,
                                    users.name,
                                    users.email,
                                    (SELECT count(tareas.id) FROM tareas WHERE tareas.responsable = users.id AND tareas.estado = 'Completada') AS 'Completadas',
                                    (SELECT count(tareas.id) FROM tareas WHERE tareas.responsable = users.id AND tareas.estado = 'En Proceso') AS 'EnProceso',
                                    (SELECT count(tareas.id) FROM tareas WHERE tareas.responsable = users.id AND tareas.estado = 'Pendiente') AS 'Pendiente'
                            FROM users;");

        return response()->json([
            'datos' => $datos,
            'estado' => 0
        ],  Response::HTTP_OK);
    }

    public function actualizarEstado(Request $request)
    {
        if ($request->post('tipo') == 'proyecto') {
            $proyecto = Proyecto::find($request->post('id'));
            $proyecto->estado = $request->post('estado');
            $proyecto->save();
        } else {
            $tarea = Tarea::find($request->post('id'));
            $tarea->estado = $request->post('estado');
            $tarea->save();

            $comentario = new Comentario();
            $comentario->tarea_id = $request->post('id');
            $comentario->contenido = $request->post('comentario');
            $comentario->usuario_id = Auth::user()->id;
            $comentario->save();
        }

        return response()->json([
            "estado" => 0,
        ], Response::HTTP_OK);
    }
}
