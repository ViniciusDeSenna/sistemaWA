<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return View('app.companies.index', ['companies'=>\App\Models\Company::getAll()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return View('app.companies.edit');
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
                'chain_of_stores' => $request->category,
            ]);

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
        return View('app.companies.edit', ['establishment' => Company::find($id)]);
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
                'time_value' => $request->value,
                'chain_of_stores' => $request->category,
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
        try {

            DB::beginTransaction();

            $establishment = Company::find($id);
            $establishment->active = false;
            $establishment->save();

            DB::commit();

            return response()->json([
                'message' => 'Estabelecimento removido com sucesso!',
                'data' => $establishment
            ], 201);

        } catch(Exception $exception) {

            DB::rollBack();

            return response()->json([
                'title' => 'Erro na ação',
                'message' => $exception->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
}
