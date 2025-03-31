<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use Illuminate\Http\Request;
use App\Models\DailyRate;
use App\Models\User;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    
    public function registers(Request $request){
        $user = Auth::user();
        
        $dailyRate = DailyRate::query()
        ->leftJoin('collaborators', 'collaborators.id', '=', 'daily_rate.collaborator_id')
        ->leftJoin('companies', 'companies.id', '=', 'daily_rate.company_id')
        ->leftJoin('sections', 'sections.id', '=', 'daily_rate.section_id')
        ->where('daily_rate.active', true)
        ->orderBy('daily_rate.company_id')
        ->orderBy('daily_rate.section_id')
        ->orderBy('daily_rate.start')
        ->select([
            'daily_rate.collaborator_id as collaborator_id',
            'daily_rate.company_id as company_id',
            'daily_rate.section_id as section_id',
            'collaborators.name as collaborators_name',
            'companies.name as company_name',
            'sections.name as section_name', // Setor
            'daily_rate.start as start',
            'daily_rate.end as end',
            'daily_rate.total_time as total_time',
            'daily_rate.pay_amount as pay_amount',
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
        
        $groupedDailyRates = $dailyRate->groupBy('company_id');

        $html = View::make('reports.registers-layout', ['dailyRate' => $groupedDailyRates, 'user' => $user])->render();
    
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();

    }

    public function dailyRates(Request $request) {

        $user = Auth::user();
    
        // $dailyRate = DailyRate::query()
        // ->leftJoin('collaborators', 'collaborators.id', '=', 'daily_rate.collaborator_id')
        // ->leftJoin('companies', 'companies.id', '=', 'daily_rate.company_id')
        // ->leftJoin('sections', 'sections.id', '=', 'daily_rate.section_id')
        // ->where('daily_rate.active', true)
        // ->orderBy('daily_rate.company_id')  // Estabelecimento
        // ->orderBy('daily_rate.section_id')  // Setor
        // ->orderBy('daily_rate.collaborator_id')  // Colaborador
        // ->select([
        //     'daily_rate.collaborator_id as collaborator_id',
        //     'daily_rate.company_id as company_id',
        //     'daily_rate.section_id as section_id',
        //     'daily_rate.leader_comission as leader_comission',
        //     'collaborators.name as collaborators_name',
        //     'companies.name as company_name',
        //     'sections.name as section_name', // Setor
        //     'daily_rate.start as start',
        //     'daily_rate.pay_amount as pay_amount',
        //     'collaborators.pix_key as pix_key'
        // ]);

            // if ($request->collaborator_id) {
            //     $dailyRate->whereIn('daily_rate.collaborator_id', $request->collaborator_id);
            // }
            
            // if ($request->company_id) {
            //     $dailyRate->whereIn('daily_rate.company_id', $request->company_id);
            // }
            
            // if ($request->start) {
            //     $dailyRate->where('daily_rate.start', '>=', $request->start);
            // }
            
            // if ($request->end) {
            //     $dailyRate->where('daily_rate.end', '<=', $request->end);
            // }

            $dailyRate = DailyRate::query()->select([
                'daily_rate.user.collaborator.pix_key as leader_pix_key'
            ]);

        $dailyRate = DailyRate::query()
            ->leftJoin('collaborators', 'collaborators.id', '=', 'daily_rate.collaborator_id')  // Colaborador que trabalhou na diária
            ->leftJoin('companies', 'companies.id', '=', 'daily_rate.company_id')
            ->leftJoin('sections', 'sections.id', '=', 'daily_rate.section_id')
            ->leftJoin('users', 'users.id', '=', 'daily_rate.user_id')  // Associando a tabela users ao campo user_id da daily_rate
            ->leftJoin('collaborators as user_collaborator', 'user_collaborator.id', '=', 'users.collaborator_id') // Colaborador do usuário (responsável pelo registro)
            ->where('daily_rate.active', true)
            ->select([
                'daily_rate.collaborator_id as collaborator_id', // Colaborador da diária
                'daily_rate.company_id as company_id',
                'daily_rate.section_id as section_id',
                'daily_rate.user_id as user_id',
                'collaborators.name as collaborators_name',
                'companies.name as company_name',
                'sections.name as section_name',
                'daily_rate.start as start',
                'daily_rate.pay_amount as pay_amount',
                'collaborators.pix_key as pix_key',  // Chave PIX do colaborador que trabalhou
                'users.collaborator_id as user_collaborator_id',  // Colaborador do usuário que registrou
                'user_collaborator.pix_key as user_pix_key',  // Chave PIX do colaborador responsável pelo registro
                'daily_rate.leader_comission as leader_comission',
                'users.name as user_name',  // Nome do usuário que fez o registro
                'user_collaborator.pix_key as leader_pix_key' // Chave PIX do líder (usuário que registrou a diária)
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
        
        $leaderCommissions = (clone $dailyRate)
            ->where('collaborators.is_leader', '=', false) //  não recebe caso o colaborador que trabalhe na diária seja o próprio ou outro líder
            ->select([
                'users.name as leader_name',
                'user_collaborator.pix_key as leader_pix_key',
                DB::raw('SUM(daily_rate.leader_comission) as total_leader_comission')
            ])
            ->groupBy('daily_rate.user_id', 'user_collaborator.pix_key')
            ->orderBy('total_leader_comission', 'desc')
            ->get();

        $dailyRate = $dailyRate->get();

        $groupedDailyRates = $dailyRate->groupBy('company_id');
    
        $html = View::make('reports.daily-rate-layout', ['dailyRate' => $groupedDailyRates, 'user' => $user, 'leaderCommissions' => $leaderCommissions])->render();
    
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function financial(Request $request) {

        $user = Auth::user();

        $dailyRate = DailyRate::query()
        ->leftJoin('collaborators', 'collaborators.id', '=', 'daily_rate.collaborator_id')
        ->leftJoin('companies', 'companies.id', '=', 'daily_rate.company_id')
        ->leftJoin('sections', 'sections.id', '=', 'daily_rate.section_id')
        ->where('daily_rate.active', true)
        ->orderBy('daily_rate.company_id')
        ->orderBy('daily_rate.section_id')
        ->orderBy('daily_rate.collaborator_id') 
        ->select([
            'daily_rate.collaborator_id as collaborator_id',
            'daily_rate.company_id as company_id',
            'daily_rate.section_id as section_id',
            'collaborators.name as collaborators_name',
            'companies.name as companies_name',
            'sections.name as section_name',
            'daily_rate.start as start',
            'daily_rate.pay_amount as pay_amount',
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

        $html = View::make('reports.financial-layout', ['dailyRate' => $groupedDailyRates, 'user' => $user])->render();
    
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
}
