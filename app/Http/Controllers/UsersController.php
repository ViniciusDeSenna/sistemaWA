<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(){
        return View('app.users.index', ['users' => User::getActive()]);
    }

    public function create(){
        return View('app.users.edit');
    }

    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);            
        
            if ($validator->fails()) {
                return response()->json([
                    'message' => implode("\n", $validator->errors()->all()),
                ], 422);
            }
        
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));
        
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
