<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(){
        return View('app.users.index');
    }

    public function create(){
        return View('app.users.edit');
    }

    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'email.email' => 'O campo e-mail deve ser um endereço de e-mail válido.',
                'email.required' => 'O campo e-mail é obrigatório.',
                'email.unique' => 'Este e-mail já está em uso.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
                'password.confirmed' => 'As senhas não coincidem.',
            ]);            
        
            if ($validator->fails()) {
                return response()->json([
                    'message' => implode("\n", $validator->errors()->all()),
                ], 422);
            }
        
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
        
            return response()->json([
                'message' => 'Usuário cadastrado com sucesso!',
                'data' => $user
            ], 201);

        } catch(Exception $exception) {
            return response()->json([
                'title' => 'Erro na validação',
                'message' => $exception->getMessage(),
                'type' => 'error'
            ], $exception->getCode());
        }
    }
    

    public function edit(){
        return View('app.users.edit');
    }

    public function update(){
        return View('app.users.edit');
    }

    public function destroy(){
        return View('app.users.edit');
    }
}
