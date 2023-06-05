<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public $reglas = [
        'name' => 'required',
        'email' => 'required|email|users',
        'password' => 'min:6',
    ];
    public $mensajes = [
        'required' => 'El campo :attribute es obligatorio.',
        'email' => 'El campo :attribute debe ser una dirección de correo válida.',
        'min' => 'El campo :attribute debe tener al menos :min caracteres.',
        'unique' => 'El campo :attribute ya está en uso.',
    ];

    public function index()
    {
        return response()->json(
            [
                "data" => User::all(),
                "estado" => 0
            ],
            Response::HTTP_OK
        );
    }

    public function show(User $user)
    {
        return response()->json(
            [
                "data" => $user,
                "estado" => 0
            ],
            Response::HTTP_OK
        );
    }


    public function login(Request $request)
    {
        $data = [
            'email' =>  $request->post('email'),
            'password' => $request->post('password')
        ];

        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser válido.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
        ]);


        if ($validator->fails()) {
            return response()->json(
                [
                    "data" =>  $validator->errors(),
                    "estado" => 1
                ],
                Response::HTTP_OK
            );
        }

        if (Auth::attempt($data)) {
            $user = Auth::user();

            $loginData['token'] = $user->createToken('IDOToken')->accessToken;


            return response()->json(
                [
                    "user" => $user,
                    "token" => $loginData['token'],
                    "estado" => 0
                ],
                Response::HTTP_OK
            );
        } else {
            return response()->json(
                [
                    "data" =>  ['credenciales' => 'Credenciales incorrectas'],
                    "estado" => 1
                ],
                Response::HTTP_OK
            );
        }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(
            [
                "data" => 'Sesion cerrada correctamente',
                "errors" => ""
            ],
            200
        );
    }



    public function store(Request $request)
    {
        $data = $request->post();
        $data['password'] = 'password';
        $this->reglas['email'] = 'required|email|unique:users';
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

        $user = User::create($data);

        return response()->json(
            [
                "user" => $user,
                "estado" => 0
            ],
            Response::HTTP_OK
        );
    }


    public function update(Request $request, User $user)
    {
        $data = $request->post();
        $this->reglas['email'] = 'required|email|unique:users,email,' . $user->id;
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
        $user->update($data);

        return response()->json(
            [
                "user" => $user,
                "estado" => 0
            ],
            Response::HTTP_OK
        );
    }


    public function eliminar(User $user)
    {
        $user->delete();
        DB::delete('delete from estudiantes where identidad = ?', [$user->email]);

        return response()->json(
            [
                "data" => "Eliminado",
                "message" => "Actualizado con Exito"
            ],
            200
        );
    }
}
