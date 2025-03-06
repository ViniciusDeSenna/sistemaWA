<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyRate;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class ReportsController extends Controller
{
    public function dailyRates(Request $request) {

        $user = Auth::user();

        $dailyRate = DailyRate::query()
            ->leftJoin('collaborators', 'collaborators.id', '=', 'daily_rate.collaborator_id')
            ->leftJoin('companies', 'companies.id', '=', 'daily_rate.company_id')
            ->where('daily_rate.active', '=', true)
            ->orderBy('daily_rate.collaborator_id')
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
                'daily_rate.total_value as daily_rate_total_value',
                'collaborators.pix_key as collaborators_pix_key'
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


        $dailyRate = $dailyRate->get();

        $groupedDailyRates = $dailyRate->groupBy('daily_rate_collaborator_id');

        $html = View::make('reports.daily-rate-layout', ['dailyRate' => $groupedDailyRates, 'user' => $user])->render();
    
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function financial(Request $request) {

        $user = Auth::user();

        $dailyRate = DailyRate::query()
            ->leftJoin('collaborators', 'collaborators.id', '=', 'daily_rate.collaborator_id')
            ->leftJoin('companies', 'companies.id', '=', 'daily_rate.company_id')
            ->where('daily_rate.active', '=', true)
            ->orderBy('daily_rate.collaborator_id')
            ->select([
                'daily_rate.id as daily_rate_id',
                'daily_rate.collaborator_id as daily_rate_collaborator_id',
                'daily_rate.company_id as daily_rate_company_id',
                'collaborators.name as collaborators_name',
                'companies.name as companies_name',
                'daily_rate.start as daily_rate_start',
                'daily_rate.start_interval as daily_rate_start_interval',
                'daily_rate.end_interval as daily_rate_end_interval',
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


        $dailyRate = $dailyRate->get();

        $groupedDailyRates = $dailyRate->groupBy('daily_rate_collaborator_id');

        $html = View::make('reports.daily-rate-layout', ['dailyRate' => $groupedDailyRates, 'user' => $user])->render();
    
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
