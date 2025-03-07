<?php

namespace App\Http\Controllers;

use App\BlueUtils\Money;
use App\BlueUtils\Number;
use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return View('app.companies.index');
    }

    public function table(Request $request){
        $companies = Company::query()
            ->where('active', '=', true)
            ->orderBy('name');
        
        return DataTables::of($companies)
            ->addColumn('name', function ($company) {
                return $company->name;
            })
            ->addColumn('document', function ($company) {
                return $company->document;
            })
            ->addColumn('actions', function ($company) {
                return '
                    <div class="demo-inline-spacing">
                        <a type="button" class="btn btn-icon btn-primary" href="'. route('companies.edit', [$company->id]) . '">
                            <span class="tf-icons bx bx-pencil"></span>
                        </a>
                        <button type="button" class="btn btn-icon btn-danger" onclick="remove(' . $company->id . ')">
                            <span class="tf-icons bx bx-trash"></span>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['actions']) // Permite renderizar HTML no DataTables
            ->make(true);
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

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'name.string' => 'O nome deve ser um texto válido.',
                'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'message' => implode("\n", $validator->errors()->all()),
                ], 422);
            }

            Company::create([
                'name' => $request->name,
                'document' => Number::onlyNumber($request->document),
                'time_value' => Money::unformat($request->value),
                'chain_of_stores' => $request->category,
                'observation' => $request->observation,
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
            ], [
                'name.required' => 'O campo nome é obrigatório.',
                'name.string' => 'O nome deve ser um texto válido.',
                'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'message' => implode("\n", $validator->errors()->all()),
                ], 422);
            }

            $establishment = Company::findOrFail($id);
            $establishment->update([
                'name' => $request->name,
                'document' => Number::onlyNumber($request->document),
                'time_value' => Money::unformat($request->value),
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

    public function getHourlyRate($id) 
    {
        try {
            $company = Company::query()->where('id', '=', $id)->first();
            return $company?->time_value ?? 0;
        } catch (Exception $exception) {
            return 0;
        }
    }
}
