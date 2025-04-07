<?php

namespace App\Livewire;

use App\Http\Controllers\ReportsController;
use App\Models\Cost;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Modelable;

class FinantialResults extends Component
{
    public float $total_earned = 0;
    public float $total_costs = 0;
    public float $profit = 0;


    public string $start;
    public string $end;
    public string $pivotDay;
    public string $currentPeriod = 'week'; // Armazena o período atual

    public array $sections_array = [];
    public array $cities_array = [];
    public array $companies_array = [];

    public array $cost = [];
    #[Modelable]
    public ?string $costCategory = null;
    public bool $costCollapseOpen = false;

    public function mount()
    {
        $this->generateTables();
        $this->pivotDay = Carbon::today()->toDateString();
        $this->setFilter('week');
    }

    public function generateTables()
    {
        $this->sectionsTable();
        $this->citiesTable();
        $this->companiesTable();
    }

    public function saveCusto() {
        Cost::create($this->cost);
        $this->cost = [];
    }

    public function sectionsTable()
    {
        $sections = DB::table('sections')->get();
        
        foreach ($sections as $section) {
            $this->sections_array[$section->id] = [
                'id' => $section->id,
                'name' => $section->name,
                'dailyRateCount' => 0,
                'totalEarned' => 0,
                'totalCost' => 0,
                'totalProfit' => 0,
            ];
        }
    }

    public function citiesTable()
    {
        $cities = DB::table('companies')->select('city')->distinct()->get();
        
        foreach ($cities as $city) {
            $this->cities_array[$city->city] = [
                'id' => null,
                'name' => $city->city,
                'dailyRateCount' => 0,
                'totalEarned' => 0,
                'totalCost' => 0,
                'totalProfit' => 0,
            ];
        }
    }

    public function companiesTable()
    {
        $companies = DB::table('companies')->get();
        
        foreach ($companies as $company) {
            $this->companies_array[$company->id] = [
                'id' => $company->id,
                'name' => $company->name,
                'dailyRateCount' => 0,
                'totalEarned' => 0,
                'totalCost' => 0,
                'totalProfit' => 0,
            ];
        }
    }

    public function setFilter($period)
    {
        $this->currentPeriod = $period; // Salva o período atual
        $today = Carbon::today();
    
        switch ($period) {
            case 'day':
                $this->start = $today->toDateString();
                $this->end = $today->toDateString();
                break;
            case 'week':
                $this->start = $today->startOfWeek(Carbon::SUNDAY)->toDateString();
                $this->end = $today->endOfWeek(Carbon::SATURDAY)->toDateString();
                break;
            case 'month':
                $this->start = $today->startOfMonth()->toDateString();
                $this->end = $today->endOfMonth()->toDateString();
                break;
            case 'year':
                $this->start = $today->startOfYear()->toDateString();
                $this->end = $today->endOfYear()->toDateString();
                break;
        }
    
        $this->fetchFinancialData();
    }
    
    public function navigatePeriod($direction)
    {
        $step = $direction === 'next' ? 1 : -1;
    
        switch ($this->currentPeriod) {
            case 'day':
                $this->start = Carbon::parse($this->start)->addDays($step)->toDateString();
                $this->end = $this->start;
                break;
            case 'week':
                $this->start = Carbon::parse($this->start)->addWeeks($step)->startOfWeek(Carbon::SUNDAY)->toDateString();
                $this->end = Carbon::parse($this->end)->addWeeks($step)->endOfWeek(Carbon::SATURDAY)->toDateString();
                break;
            case 'month':
                $this->start = Carbon::parse($this->start)->addMonths($step)->startOfMonth()->toDateString();
                $this->end = Carbon::parse($this->end)->addMonths($step)->endOfMonth()->toDateString();
                break;
            case 'year':
                $this->start = Carbon::parse($this->start)->addYears($step)->startOfYear()->toDateString();
                $this->end = Carbon::parse($this->end)->addYears($step)->endOfYear()->toDateString();
                break;
        }
    
        $this->fetchFinancialData();
    }

    public function fetchFinancialData()
    {
        $sections_array = [];
        $companies_array = [];
        $cities_array = [];
        $this->generateTables();
        $this->total_earned = 0;
        $this->total_costs = 0;
        $this->profit = 0;
    
        $dailyRates = DB::table('daily_rate')
            ->whereBetween('start', [
                Carbon::parse($this->start)->startOfDay()->toDateTimeString(),
                Carbon::parse($this->end)->endOfDay()->toDateTimeString()
            ])
            ->get();
    
        foreach ($dailyRates as $rate) {
            $earned = (float) ($rate->earned);
            
            $earned_total = (float) (($rate->earned ?? 0));
            $tax_percentage = (float) ($rate->tax_paid ?? 0) / 100;
    
            $cost = (float) (
                $rate->pay_amount 
                + $rate->transportation 
                + $rate->leader_comission 
                + $rate->inss_paid 
                + ($earned_total * $tax_percentage)
            );
            $profit = (float) $rate->profit;
    
            $this->total_earned += $earned;
            $this->total_costs += $cost;
            $this->profit += $profit;
    
            if (isset($this->sections_array[$rate->section_id])) {
                $this->sections_array[$rate->section_id]['dailyRateCount']++;
                $this->sections_array[$rate->section_id]['totalEarned'] += $earned;
                $this->sections_array[$rate->section_id]['totalCost'] += $cost;
                $this->sections_array[$rate->section_id]['totalProfit'] += $profit;
            }
    
            if (isset($this->companies_array[$rate->company_id])) {
                $this->companies_array[$rate->company_id]['dailyRateCount']++;
                $this->companies_array[$rate->company_id]['totalEarned'] += $earned;
                $this->companies_array[$rate->company_id]['totalCost'] += $cost;
                $this->companies_array[$rate->company_id]['totalProfit'] += $profit;
            }
    
            $company = DB::table('companies')->where('id', $rate->company_id)->first();
            if ($company && isset($this->cities_array[$company->city])) {
                $this->cities_array[$company->city]['dailyRateCount']++;
                $this->cities_array[$company->city]['totalEarned'] += $earned;
                $this->cities_array[$company->city]['totalCost'] += $cost;
                $this->cities_array[$company->city]['totalProfit'] += $profit;
            }
        }
    }
    public function adicionarCusto(){
        $this->emit('showAddCostModal');


    }
    public function render()
    {
        return view('livewire.finantial-results', [
            'sections_array' => array_values($this->sections_array),
            'companies_array' => array_values($this->companies_array),
            'cities_array' => array_values($this->cities_array),
        ])->layout('layouts.app');
    }
    public function gerarRelatorioFinanceiro()
    {
        return redirect()->route('relatorio.financeiro', [
            'start' => $this->start,
            'end' => $this->end,
        ]);
    }    
}
