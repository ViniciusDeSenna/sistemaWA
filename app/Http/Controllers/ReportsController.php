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
                'daily_rate.collaborator_id as collaborator_id',
                'daily_rate.company_id as company_id',
                'collaborators.name as collaborators_name',
                'companies.name as companies_name',
                'daily_rate.start as start',
                'daily_rate.end as end',
                'daily_rate.total_time as total_time',
                'daily_rate.collaborator_participation as collaborator_participation',
                'collaborators.pix_key as pix_key'
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

        $groupedDailyRates = $dailyRate->groupBy('collaborator_id');

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
                'daily_rate.collaborator_id as collaborator_id',
                'daily_rate.company_id as company_id',
                'collaborators.name as collaborators_name',
                'companies.name as companies_name',
                'daily_rate.start as start',
                'daily_rate.end as end',
                'daily_rate.total_time as total_time',
                'daily_rate.hourly_rate as hourly_rate',
                'daily_rate.costs as costs',
                'daily_rate.addition as addition',
                'daily_rate.collaborator_participation as collaborator_participation',
                'daily_rate.total as total',
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

        $groupedDailyRates = $dailyRate->groupBy('collaborator_id');

        $html = View::make('reports.financial-layout', ['dailyRate' => $groupedDailyRates, 'user' => $user])->render();
    
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
