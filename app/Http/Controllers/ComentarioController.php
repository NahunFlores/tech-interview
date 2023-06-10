<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Illuminate\Http\Response;

class ComentarioController extends Controller
{
    public function index($id)
    {
        $comentarios = Comentario::join('users','comentarios.usuario_id','=','users.id')
                        ->leftJoin('tareas','comentarios.tarea_id','=','tareas.id')
                        ->where('tareas.id','=',$id)
                        ->select('comentarios.contenido','users.email','comentarios.created_at')
                        ->get();

        return response()->json([
            'data' => $comentarios,
            "estado" => 0
        ], Response::HTTP_OK);
    }

}
