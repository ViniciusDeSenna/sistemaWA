<?php

namespace App\Http\Controllers;

use App\BlueUtils\Money;
use App\BlueUtils\Time;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Company;
use App\Models\DailyRate;
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
                'daily_rate.start as daily_rate_start',
                'daily_rate.end as daily_rate_end',
                'daily_rate.daily_total_time as daily_rate_daily_total_time',
                'daily_rate.hourly_rate as daily_rate_hourly_rate',
                'daily_rate.addition as daily_rate_addition',
                'daily_rate.costs as daily_rate_costs',
                'daily_rate.total as daily_rate_total',
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
                return mb_strimwidth($daily->collaborators_name ?? '', 0, 20, '...');
            })
            ->addColumn('companies_name', function ($daily) {
                return mb_strimwidth($daily->companies_name ?? '', 0, 20, '...');
            })
            ->addColumn('daily_rate_start', function ($daily) {
                if (isset($daily->daily_rate_start)) {
                    return Carbon::parse($daily->daily_rate_start)->format('d/m/Y H:i:s');
                } else {
                    return '--/--/-- --:--:--';
                }
            })
            ->addColumn('daily_rate_end', function ($daily) {
                if (isset($daily->daily_rate_end)) {
                    return Carbon::parse($daily->daily_rate_end)->format('d/m/Y H:i:s');
                } else {
                    return '--/--/-- --:--:--';
                }
            })
            ->addColumn('daily_rate_daily_total_time', function ($daily) {
                return str_replace('.', ':', $daily->daily_rate_daily_total_time);
            })
            ->addColumn('daily_rate_hourly_rate', function ($daily) use ($user) {
                if ($user->can('Visualizar e inserir informações financeiras nas diárias')) {
                    return Money::format($daily->daily_rate_hourly_rate ?? '0', 'R$ ', 2, ',', '.');
                } else {
                    return 'R$ --,--';
                }
            })
            ->addColumn('daily_rate_addition', function ($daily) use ($user) {
                if ($user->can('Visualizar e inserir informações financeiras nas diárias')) {
                    return Money::format($daily->daily_rate_addition ?? '0', 'R$ ', 2, ',', '.');
                } else {
                    return 'R$ --,--';
                }
            })
            ->addColumn('daily_rate_costs', function ($daily) use ($user) {
                if ($user->can('Visualizar e inserir informações financeiras nas diárias')) {
                    return Money::format($daily->daily_rate_costs ?? '0', 'R$ ', 2, ',', '.');
                } else {
                    return 'R$ --,--';
                }
            })
            ->addColumn('daily_rate_total', function ($daily) use ($user) {
                if ($user->can('Visualizar e inserir informações financeiras nas diárias')) {
                    return Money::format($daily->daily_rate_total ?? '0', 'R$ ', 2, ',', '.');
                } else {
                    return 'R$ --,--';
                }
            })
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
            'companies' => Company::getActive()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {


            DB::beginTransaction();

            $dailyRate = new DailyRate();
            $dailyRate->collaborator_id = $request->collaborator_id;
            $dailyRate->company_id = $request->company_id;
            $dailyRate->start = $request->start;
            $dailyRate->start_interval = $request->start_interval;
            $dailyRate->end_interval = $request->end_interval;
            $dailyRate->end = $request->end;
            $dailyRate->daily_total_time = $request->daily_total_time;
            $dailyRate->hourly_rate = $request->hourly_rate;
            $dailyRate->total_value = Time::convertTimeToDecimal($request->daily_total_time) * $request->hourly_rate;
            $dailyRate->costs = $request->costs;
            $dailyRate->costs_description = $request->costs_description;
            $dailyRate->addition = $request->addition;
            $dailyRate->addition_description = $request->addition_description;
            $dailyRate->total = $request->total;
            $dailyRate->pix_key = $request->pix_key;
            $dailyRate->observation = $request->observation;
            $dailyRate->save();

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
            'companies' => Company::getActive()
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
            $dailyRate->collaborator_id = $request->collaborator_id;
            $dailyRate->company_id = $request->company_id;
            $dailyRate->start = $request->start;
            $dailyRate->start_interval = $request->start_interval;
            $dailyRate->end_interval = $request->end_interval;
            $dailyRate->end = $request->end;
            $dailyRate->daily_total_time = $request->daily_total_time;
            $dailyRate->hourly_rate = $request->hourly_rate;
            $dailyRate->costs = $request->costs;
            $dailyRate->costs_description = $request->costs_description;
            $dailyRate->addition = $request->addition;
            $dailyRate->addition_description = $request->addition_description;
            $dailyRate->total = $request->total;
            $dailyRate->pix_key = $request->pix_key;
            $dailyRate->observation = $request->observation;
            $dailyRate->save();

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
}
