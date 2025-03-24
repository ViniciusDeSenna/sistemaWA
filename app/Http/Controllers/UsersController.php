<?php

namespace App\Http\Controllers;

use App\Models\Collaborator;
use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    public function index(){
        return View('app.users.index');
    }

    public function table(Request $request){
        $users = User::query()
            ->where('active', '=', true)
            ->orderBy('name');
        
        return DataTables::of($users)
            ->addColumn('name', function ($user) {
                return $user->name;
            })
            ->addColumn('actions', function ($user) {
                return '
                    <div class="demo-inline-spacing">
                        <a type="button" class="btn btn-icon btn-primary" href="'. route('users.edit', [$user->id]) . '">
                            <span class="tf-icons bx bx-pencil"></span>
                        </a>
                        <button type="button" class="btn btn-icon btn-danger" onclick="remove(' . $user->id . ')">
                            <span class="tf-icons bx bx-trash"></span>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['actions']) // Permite renderizar HTML no DataTables
            ->make(true);
    }

    public function create(){

        return View('app.users.edit', ['permissions' => Permission::all(), 'collaborators' => Collaborator::getActiveLeaders()]);
    }

    
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required', 
                    'string', 
                    'lowercase', 
                    'email', 
                    'max:255', 
                    Rule::unique(User::class)->where('active', true),
                ],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'name.string' => 'O nome deve ser um texto válido.',
                'name.max' => 'O nome não pode ter mais de 255 caracteres.',
                
                'email.required' => 'O campo e-mail é obrigatório.',
                'email.string' => 'O e-mail deve ser um texto válido.',
                'email.lowercase' => 'O e-mail deve estar em letras minúsculas.',
                'email.email' => 'O e-mail informado não é válido.',
                'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
                'email.unique' => 'Este e-mail já está em uso.',
                
                'password.required' => 'O campo senha é obrigatório.',
                'password.confirmed' => 'A confirmação da senha não confere.',
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
                'collaborator_id' => $request->collaborator_id,
            ]);

            event(new Registered($user));
        
            // Seta as pemissoes no usuário
            $user->givePermissionTo(array_keys($request->permissions));

            DB::commit();

            return response()->json([
                'title' => 'Sucesso!',
                'message' => 'Usuário cadastrado com sucesso!',
                'type' => 'success'
            ], 201);

        } catch(Exception $exception) {

            DB::rollBack();

            return response()->json([
                'title' => 'Erro na validação',
                'message' => $exception->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
    

    public function edit($id){
        return View('app.users.edit', ['user' => User::find($id), 'permissions' => Permission::all()]);
    }

    public function update(Request $request, $id){
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required', 
                    'string', 
                    'lowercase', 
                    'email', 
                    'max:255', 
                    Rule::unique(User::class)
                        ->where('active', true)
                        ->whereNot('id', $id),
                ],
                'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'name.string' => 'O nome deve ser um texto válido.',
                'name.max' => 'O nome não pode ter mais de 255 caracteres.',
                
                'email.required' => 'O campo e-mail é obrigatório.',
                'email.string' => 'O e-mail deve ser um texto válido.',
                'email.lowercase' => 'O e-mail deve estar em letras minúsculas.',
                'email.email' => 'O e-mail informado não é válido.',
                'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
                'email.unique' => 'Este e-mail já está em uso por outro usuário.',
            
                'password.confirmed' => 'A confirmação da senha não confere.',
            ]);          
        
            if ($validator->fails()) {
                return response()->json([
                    'message' => implode("\n", $validator->errors()->all()),
                ], 422);
            }
        
            $user = User::findOrFail($id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
                'collaborator_id' => $request->collaborator_id,
            
            ]);
        
            // Seta as pemissoes no usuário
            $user->syncPermissions(array_keys($request->permissions));

            DB::commit();

            return response()->json([
                'title' => 'Sucesso!',
                'message' => 'Usuário atualizado com sucesso!',
                'type' => 'success'
            ], 201);

        } catch(Exception $exception) {

            DB::rollBack();

            return response()->json([
                'title' => 'Erro na validação',
                'message' => $exception->getMessage(),
                'type' => 'error'
            ], 500);
        }  
    }

    public function destroy($id){
        try {

            DB::beginTransaction();

            $user = User::find($id);
            $user->active = false;
            $user->save();

            DB::commit();

            return response()->json([
                'message' => 'Usuário removido com sucesso!',
                'data' => $user
            ], 201);

        } catch(Exception $exception) {

            DB::rollBack();

            return response()->json([
                'title' => 'Erro na validação',
                'message' => $exception->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
}
