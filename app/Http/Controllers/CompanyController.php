<?php

namespace App\Http\Controllers;

use App\BlueUtils\Money;
use App\BlueUtils\Number;
use App\Models\Company;
use App\Models\CompanyHasSection;
use App\Models\Section;
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
        $sections = Section::all();
        return View('app.companies.edit', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
    {
        //dd($request->all());
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

            $company = Company::create([
                'name' => $request->name,
                'document' => Number::onlyNumber($request->document),
                'chain_of_stores' => $request->category,
                'city' => $request->city,
                'uniforms_laid'=> $request->uniforms_laid ?? 0,
                'observation' => $request->observation,
            ]);
            if($request->section_id) {
                foreach($request->section_id as $section_id){

                    CompanyHasSection::updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'section_id' => $section_id,
                    ],
                    [
                        'company_id' => $company->id,
                        'section_id' => $section_id,
                        'earned' => $request->earned[$section_id],
                        'employeePay' => $request->diaria[$section_id],
                        'leaderPay' => $request->lider[$section_id],
                        'extra' => (int) $request->extra[$section_id],
                        'leaderComission' => $request->comissao[$section_id],
                        'perHour' => isset($request->perHour[$section_id]) && $request->perHour[$section_id] === 'on',
                        'active' => true,
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'title' => 'Sucesso!',
                'message' => 'Estabelecimento cadastrado com sucesso!',
                'type' => 'success',
                'company_id' => $company->id
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
        $company = Company::find($id); 
        $sections = Section::all();
    
        return view('app.companies.edit', compact('company', 'sections'));
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

            $company = Company::findOrFail($id);
            $company->update([
                'name' => $request->name,
                'document' => Number::onlyNumber($request->document),
                'city' => $request->city,
                'uniforms_laid'=> ($request->uniforms_laid),
                'chain_of_stores' => $request->category,
                'observation' => $request->observation,
            ]);
            
            foreach($request->section_id as $section_id){
                CompanyHasSection::updateOrCreate(
                    [
                    'company_id' => $company->id,
                    'section_id' => $section_id,
                ],
                [
                    'company_id' => $company->id,
                    'section_id' => $section_id,
                    'earned' => $request->earned[$section_id],
                    'employeePay' => $request->diaria[$section_id],
                    'leaderPay' => $request->lider[$section_id],
                    'extra' => (int) $request->extra[$section_id],
                    'leaderComission' => $request->comissao[$section_id],
                    'perHour' => isset($request->perHour[$section_id]) && $request->perHour[$section_id] === 'on',
                    'active' => true,
                ]);
            }

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
