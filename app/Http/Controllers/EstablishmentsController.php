<?php
namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Establishment;
use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Email;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
class EstablishmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return View('app.establishments.index', ['establishments'=>\App\Models\Company::getAll()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return View('app.establishments.edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB:: beginTransaction();

            $validator = Validator:: make ($request->all(),[
                'name' =>['required', 'string', 'max:255'],]);
            if ($validator->fails()) {
                return response()->json([
                'message' => implode("\n", $validator->errors()->all()),
                ], 422);
            }
            Company::create([
                'name' => $request->name,
                'document' => $request->document,
                'time_value' => $request->value,
                'observation' => $request->observation,
                'chain_of_stores' => $request->category ?? "indefinido",
            ]);
            dd($request->all());

            DB::commit();

            return response()->json([
                'title' => 'Sucesso!',
                'message' => 'Estabelecimento cadastrado com sucesso!',
                'type' => 'success'
            ], 201);

        } catch(Exception $exception){
            DB::rollBack();
            return response()->json([
                'title'=>'Erro na validação',
                'message'=>$exception->getMessage(),
                'type' => 'error'],status: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       // return View('app.establishments.edit', ['establishments' => Company::getActive());

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
            ]);            
        
            if ($validator->fails()) {
                return response()->json([
                    'message' => implode("\n", $validator->errors()->all()),
                ], 422);
            }

            $establishment = Company::findOrFail($id);
            $establishment->update([
                'name' => $request->name,
                'document' => $request->document,
                'time_value' => $request->time_value,
                'observation' => $request->observation,
            ]);

            DB::commit();

            return response()->json([
                'title' => 'Sucesso!',
                'message' => 'Colaborador cadastrado com sucesso!',
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
