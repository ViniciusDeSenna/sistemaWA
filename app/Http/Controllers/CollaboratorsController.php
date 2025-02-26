<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CollaboratorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return View('app.collaborators.index', ['collaborators' => Collaborator::getActive()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return View('app.collaborators.edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
        
            Collaborator::create([
                'name' => $request->name,
                'document' => $request->document,
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
        return View('app.collaborators.edit', ['collaborator' => Collaborator::find($id)]);
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

            $collaborator = Collaborator::findOrFail($id);
            $collaborator->update([
                'name' => $request->name,
                'document' => $request->document,
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

            $user = Collaborator::find($id);
            $user->active = false;
            $user->save();

            DB::commit();

            return response()->json([
                'message' => 'Colaborador removido com sucesso!',
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
