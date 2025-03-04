<?php

namespace App\Http\Controllers;

use App\BlueUtils\Money;
use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use App\Models\Company;
use App\Models\DailyRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Yajra\DataTables\Facades\DataTables;

class DailyRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return View('app.daily-rate.index');
    }

    public function table(Request $request) {
        $dailyRate = DailyRate::query()
            ->leftJoin('collaborators', 'collaborators.id', '=', 'daily_rate.collaborator_id')
            ->leftJoin('companies', 'companies.id', '=', 'daily_rate.company_id')
            ->where('active', '=', true)
            ->orderBy('created_at')
            ->select([
                'collaborators.name as collaborators_name',
                'companies.name as companies_name',
                'daily_rate.date as daily_rate_date',
                'daily_rate.start as daily_rate_start',
                'daily_rate.end as daily_rate_end',
                'daily_rate.total_time as daily_rate_total_time',
                'daily_rate.hourly_rate as daily_rate_hourly_rate',
                'daily_rate.addition as daily_rate_addition',
                'daily_rate.costs as daily_rate_costs',
                'daily_rate.total as daily_rate_total',
            ]);
        
        return DataTables::of($dailyRate)
            ->addColumn('collaborators_name', function ($daily) {
                return mb_strimwidth($daily->collaborators_name, 0, 20, '...');
            })
            ->addColumn('companies_name', function ($daily) {
                return mb_strimwidth($daily->companies_name, 0, 20, '...');
            })
            ->addColumn('daily_rate_date', function ($daily) {
                return Carbon::parse($daily->daily_rate_date)->format('dd/mm/YYYY');
            })
            ->addColumn('daily_rate_start', function ($daily) {
                return $daily->daily_rate_start;
            })
            ->addColumn('daily_rate_end', function ($daily) {
                return $daily->daily_rate_end;
            })
            ->addColumn('daily_rate_total_time', function ($daily) {
                return $daily->docdaily_rate_total_timeument;
            })
            ->addColumn('daily_rate_hourly_rate', function ($daily) {
                return Money::format($daily->daily_rate_hourly_rate, 'R$ ');
            })
            ->addColumn('daily_rate_addition', function ($daily) {
                return Money::format($daily->daily_rate_addition, 'R$ ');
            })
            ->addColumn('daily_rate_costs', function ($daily) {
                return Money::format($daily->daily_rate_costs, 'R$ ');
            })
            ->addColumn('daily_rate_total', function ($daily) {
                return Money::format($daily->daily_rate_total, 'R$ ');
            })
            ->addColumn('actions', function ($daily) {
                return '
                    <div class="demo-inline-spacing">
                        <a type="button" class="btn btn-icon btn-primary" href="'. route('companies.edit', [$daily->id]) . '">
                            <span class="tf-icons bx bx-pencil"></span>
                        </a>
                        <a type="button" class="btn btn-icon btn-danger" href="javascript(0);" onclick="remove(' . $daily->id . ')">
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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function makePDF() {
        $mpdf = new Mpdf();
        $mpdf->WriteHTML('<h1>Hello world!</h1>');
        $mpdf->Output();
    }
}
