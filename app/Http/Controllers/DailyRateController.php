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
            ->leftJoin('collaborators', 'collaborators.id', '=', 'daily_rate.collaborator_id')
            ->leftJoin('companies', 'companies.id', '=', 'daily_rate.company_id')
            ->where('daily_rate.active', '=', true)
            ->orderBy('daily_rate.created_at')
            ->select([
                'daily_rate.id as daily_rate_id',
                'daily_rate.collaborator_id as daily_rate_collaborator_id',
                'daily_rate.company_id as daily_rate_company_id',
                'collaborators.name as collaborators_name',
                'companies.name as companies_name',
                'daily_rate.start as start',
                'daily_rate.end as end',
                'daily_rate.total_time as total_time',
                'daily_rate.addition as addition',
                'daily_rate.transportation as transportation',
                'daily_rate.feeding as feeding',
                'daily_rate.earned as earned',
                'daily_rate.profit as profit',
            ]);

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
            ->addColumn('collaborators_name', function ($daily) {
                return mb_strimwidth($daily->collaborators_name ?? 'Não Informado', 0, 20, '...');
            })
            ->addColumn('companies_name', function ($daily) {
                return mb_strimwidth($daily->companies_name ?? 'Não Informado', 0, 20, '...');
            })
            ->addColumn('start', function ($daily) {
                if (isset($daily->start)) {
                    return Carbon::parse($daily->start)->format('d/m/Y H:i:s');
                } else {
                    return '--/--/-- --:--:--';
                }
            })
            ->addColumn('end', function ($daily) {
                if (isset($daily->end)) {
                    return Carbon::parse($daily->end)->format('d/m/Y H:i:s');
                } else {
                    return '--/--/-- --:--:--';
                }
            })
            ->addColumn('total_time', function ($daily) {
                return str_replace('.', ':', $daily->total_time);
            })
//            ->addColumn('hourly_rate', function ($daily) use ($user) {
//                if ($user->can('Visualizar e inserir informações financeiras nas diárias')) {
//                    return Money::format($daily->hourly_rate ?? '0', 'R$ ', 2, ',', '.');
//                } else {
//                    return 'R$ --,--';
//                }
//            })
//            ->addColumn('addition', function ($daily) use ($user) {
//            if ($user->can('Visualizar e inserir informações financeiras nas diárias')) {
//                    return Money::format($daily->addition ?? '0', 'R$ ', 2, ',', '.');
//                } else {
//                    return 'R$ --,--';
//                }
//            })
//            ->addColumn('transport', function ($daily) use ($user) {
//            if ($user->can('Visualizar e inserir informações financeiras nas diárias')) {
//                return Money::format($daily->costs ?? '0', 'R$ ', 2, ',', '.');
//                } else {
//                    return 'R$ --,--';
//                }
//            })
//            ->addColumn('total', function ($daily) use ($user) {
//                if ($user->can('Visualizar e inserir informações financeiras nas diárias')) {
//                    return Money::format($daily->total ?? '0', 'R$ ', 2, ',', '.');
//                } else {
//                    return 'R$ --,--';
//                }
//            })
            ->addColumn('actions', function ($daily) {
                return '
                    <div class="demo-inline-spacing">
                        <a type="button" class="btn btn-icon btn-primary" href="'. route('daily-rate.edit', [$daily->daily_rate_id]) . '">
                            <span class="tf-icons bx bx-pencil"></span>
                        </a>
                        <a type="button" class="btn btn-icon btn-danger" href="javascript(0);" onclick="remove(' . $daily->daily_rate_id . ')">
                            <span class="tf-icons bx bx-trash"></span>
                        </a>
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
        return View('app.daily-rate.edit', [
            'collaborators' => Collaborator::getActive(),
            'companies' => Company::getActive(), 
            'sections' => Section::all(),
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
            $dailyrate = DailyRate::findOrFail($request->id);
            $dailyrate-> update([
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
                
                'inss_paid' => !empty($request->addition) ? Money::unformat($request->inss_id) : 0,
                'tax_paid' => !empty($request->addition) ? Money::unformat($request->imposto_paid_id) : 0,
                
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

            $dailyRate = DailyRate::find($id);
            DailyRate::update([
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
