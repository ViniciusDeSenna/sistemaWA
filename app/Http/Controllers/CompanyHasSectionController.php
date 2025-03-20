<?php

namespace App\Http\Controllers;

use App\Models\CompanyHasSection;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyHasSectionController extends Controller
{    public function remove(Request $request)
    {
        try{
            $request->validate([
                'establishment_id' => 'required|exists:companies,id',
                'section_id' => 'required|exists:sections,id',
            ]);
    
            DB::table('company_has_section')
                ->where('company_id', $request->establishment_id)
                ->where('section_id', $request->section_id)
                ->delete();
    
            return response()->json([
                'title' => 'Sucesso!',
                'message' => 'Setor removido da empresa com sucesso!',
                'type' => 'success'
            ]);
    
        }catch(\Exception $e){
            return response()->json([
                'title' => 'Sucesso!',
                'message' => 'Setor removido da empresa com sucesso!',
                'type' => 'success'
            ]);
    }
}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeArray(Request $request)
    {
        dd($request->all());
        try {
            
            DB::beginTransaction();

            foreach ($request->sections as $sectionData) {

                $companyHasSection = CompanyHasSection::storeOrUpdateSectionArray($sectionData);
            }
            DB::commit();
    
            return response()->json([
                'title' => 'Sucesso!',
                'message' => 'Setor salvo ou atualizado com sucesso.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'title' => 'Erro!',
                'message' => 'Ocorreu um erro ao salvar ou atualizar o setor: ' . $e->getMessage(),
                'type' => 'error'
            ], 500);
        }
    }
    
    


    public function storeObject(Request $request)
    {
        $data = json_decode(json_encode($request->all()), true);
        DB::beginTransaction();
        $companyHasSection = CompanyHasSection::storeOrUpdateSectionObject($data);
        DB::commit();
        dd($request->all());
        try {
            
    
            return response()->json([
                'title' => 'Sucesso!',
                'message' => 'Setor salvo ou atualizado com sucesso.',
                'type' => 'success'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
    
            return response()->json([
                'title' => 'Erro!',
                'message' => 'Ocorreu um erro ao salvar ou atualizar o setor: ' . $e->getMessage(),
                'type' => 'error'
            ], 500);  // Status 500 para erro interno do servidor
        }
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show(CompanyHasSection $company_has_section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompanyHasSection $company_has_section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompanyHasSection $company_has_section)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanyHasSection $company_has_section)
    {
        //
    }
}
