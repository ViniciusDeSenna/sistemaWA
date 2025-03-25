<?php

namespace App\Http\Controllers;

use App\BlueUtils\Money;
use App\BlueUtils\Time;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Company;
use App\Models\CompanyHasSection;
use App\Models\DailyRate;
use App\Models\Section;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class DailyRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return View('app.daily-rate.index', [
            'collaborators' => Collaborator::getActive(),
            'companies' => Company::getActive()
        ]);
    }

    public function table(Request $request) {
        $user = Auth::user();
        $dailyRate = DailyRate::query()
            ->where('daily_rate.active', '=', true)
            ->orderBy('daily_rate.created_at');

        if ($request->collaborator_id) {
            $dailyRate->whereIn('daily_rate.collaborator_id', $request->collaborator_id);
        }
        
        if ($request->company_id) {
            $dailyRate->whereIn('daily_rate.company_id', $request->company_id);
        }
        
        if ($request->start) {
            $dailyRate->where('daily_rate.start', '>=', $request->start);
        }
        
        if ($request->end) {
            $dailyRate->where('daily_rate.end', '<=', $request->end);
        }
        
        return DataTables::of($dailyRate)
            ->addColumn('company', function ($daily) {
                return mb_strimwidth($daily->company->name ?? 'Não Informado', 0, 20, '...');
            })
            ->addColumn('section', function ($daily) {
                return mb_strimwidth($daily->section->name ?? 'Não Informado', 0, 20, '...');
            })
            ->addColumn('collaborator', function ($daily) {
                return mb_strimwidth($daily->collaborator->name ?? 'Não Informado', 0, 20, '...');
            })
            ->addColumn('start', function ($daily) {
                return $daily->start ? \Carbon\Carbon::parse($daily->start)->format('d/m/Y H:i') : 'Não Informado';
            })
            ->addColumn('end', function ($daily) {
                return $daily->end ? \Carbon\Carbon::parse($daily->end)->format('d/m/Y H:i') : 'Não Informado';
            })
                     
            ->addColumn('actions', function ($daily) {
                return '
                    <div class="demo-inline-spacing">
                        <a type="button" class="btn btn-icon btn-primary" href="'. route('daily-rate.edit', [$daily->id]) . '">
                            <span class="tf-icons bx bx-pencil"></span>
                        </a>
                        <a type="button" class="btn btn-icon btn-danger" href="javascript(0);" onclick="remove(' . $daily->id . ')">
                            <span class="tf-icons bx bx-trash"></span>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return View('app.daily-rate.edit', [
            'collaborators' => Collaborator::getActive(),
            'companies' => Company::getActive(), 
            'sections' => Section::all(),
            'dailyRate' => new DailyRate(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request->all());
        try {
            DB::beginTransaction();

            if ($request->company_id) {
                $company = Company::find($request->company_id);
            }
            DailyRate::create([
                'collaborator_id' => $request->collaborator_id,
                'section_id' => $request->sectionSelect_id,
                'company_id' => $request->company_id,
                'user_id' => $request->user_id,
                
                'start' => $request->start,
                'end' => $request->end,
                'total_time' => $request->total_time,

                'leader_comission' => !empty($request->leaderComission_id) ? Money::unformat($request->leaderComission_id) : 0,
                'transportation' => !empty($request->transport_id) ? Money::unformat($request->transport_id) : 0,
                'feeding' => !empty($request->feeding_id) ? Money::unformat($request->feeding_id) : 0,
                'addition' => !empty($request->addition) ? Money::unformat($request->addition) : 0,
                'pay_amount' => Money::unformat($request->employee_pay_id),
                
                'inss_paid' => !empty($request->inss_paid) ? Money::unformat($request->inss_paid) : 0,
                'tax_paid' => !empty($request->imposto_paid_id) ? Money::unformat($request->imposto_paid_id) : 0,
                
                'earned' => Money::unformat($request->total),
                'profit' => Money::unformat($request->total_liq),
                
                'observation' => $request->observation,
            ]);


//            $dailyRate = new DailyRate();
//            //chaves estrangeiras
//            $dailyRate->collaborator_id = $request->collaborator_id;
//            $dailyRate->company_id = $request->company_id;
//            //data e hora
//            $dailyRate->start = $request->start;
//            $dailyRate->end = $request->end;
//            $dailyRate->total_time = $request->total_time;
//            //custos 
//            
//            $dailyRate->transportation = Money::unformat($request->transportation);
//            $dailyRate->feeding = Money::unformat($request->feeding);
//
//            $dailyRate->addition = Money::unformat($request->addition);
//
//            $dailyRate->pay_amount = Money::unformat($request->employee_pay_id);
//
//            $dailyRate->earned = Money::unformat($request->total);
//            $dailyRate->profit = Money::unformat($request->total_liq);
//            
//            $dailyRate->observation = $request->observation;
//            $dailyRate->user_id = $request->user_id;
//
//            $dailyRate->save();
//
            DB::commit();

            return response()->json(['type' => 'success', 'message' => 'Cadastro realizado com sucesso!'], 201);
        } catch (Exception $e) {

            DB::rollBack();

            return response()->json(['type' => 'false', 'message' => $e->getMessage()], 500);
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
        return View('app.daily-rate.edit', [
            'dailyRate' => DailyRate::find($id),
            'collaborators' => Collaborator::getActive(),
            'companies' => Company::getActive(),
            'sections' => Section::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            DB::beginTransaction();
            DailyRate::findOrFail($id)->update([
                'collaborator_id' => $request->collaborator_id,
                'section_id' => $request->sectionSelect_id,
                'company_id' => $request->company_id,
                'user_id' => $request->user_id,
                
                'start' => $request->start,
                'end' => $request->end,
                'total_time' => $request->total_time,

                'leader_comission' => !empty($request->leaderComission_id) ? Money::unformat($request->leaderComission_id) : 0,
                'transportation' => !empty($request->transport_id) ? Money::unformat($request->transport_id) : 0,
                'feeding' => $request->feeding_id=='on'? 10 : 0,
                'addition' => !empty($request->addition) ? Money::unformat($request->addition) : 0,
                'pay_amount' => Money::unformat($request->employee_pay_id),
                
                'inss_paid' => !empty($request->addition) ? Money::unformat($request->inss_id) : 0,
                'tax_paid' => !empty($request->addition) ? Money::unformat($request->imposto_paid_id) : 0,
                
                'earned' => Money::unformat($request->total),
                'profit' => Money::unformat($request->total_liq),
                
                'observation' => $request->observation,
            ]);

            
            DB::commit();

            return response()->json(['type' => 'success', 'message' => 'Cadastro realizado com sucesso!'], 201);
        } catch (Exception $e) {

            DB::rollBack();

            return response()->json(['type' => 'false', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            DB::beginTransaction();

            $dailyRate = DailyRate::find($id);
            $dailyRate->active = false;
            $dailyRate->save();

            DB::commit();

            return response()->json([
                'message' => 'Estabelecimento removido com sucesso!',
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
    public function getCompanySections($companyId)
    {
        $sections = CompanyHasSection::where('company_id', $companyId)->get();
        
        if ($sections->isEmpty()) {
            return response()->json(['message' => 'Nenhum setor encontrado.'], 404);
        }

        return response()->json($sections);
    }
}
