<?php

namespace App\Livewire;

use App\Http\Controllers\ReportsController;
use App\Models\Cost;
use App\Models\CostCategory;
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
    public array $costs_array = [];

    public $costCategories;

    public array $cost = [
        'category_id' => null,
        'description' => null,
        'value' => null,
        'date' => null,
    ];
    
    public bool $costCollapseOpen = false;

    public function mount()
    {
        $this->costCategories = CostCategory::all();
        $this->pivotDay = Carbon::today()->toDateString();
        $this->setFilter('week');
        //$this->generateTables();
    }

    public function setInitialPeriod()
    {
        // Exemplo: para filtro mensal
        $this->start = now()->startOfMonth()->toDateString();
        $this->end = now()->endOfMonth()->toDateString();
}




    public function generateTables()
    {
        $this->sectionsTable();
        $this->citiesTable();
        $this->companiesTable();
        $this->costsTable();
    }

    public function saveCusto() {

        $this->costCollapseOpen = true;

        if (is_string($this->cost['value'])) {
            $this->cost['value'] = str_replace(',', '.', $this->cost['value']);
        }

        $this->validate([
            'cost.category_id' => 'required|filled|max:255',
            'cost.description' => 'required|string|max:255',
            'cost.value' => 'required|numeric|min:0',
            'cost.date' => 'required|date',
        ],
        [
            'cost.category_id.required' => 'O campo categoria é obrigatório.',
            'cost.category_id.filled' => 'O campo categoria é obrigatório.',
            'cost.category_id.max' => 'O campo categoria deve ter no máximo 255 caracteres.',
            'cost.description.required' => 'O campo descrição é obrigatório.',
            'cost.value.required' => 'O campo valor é obrigatório.',
            'cost.value.numeric' => 'O campo valor deve ser um número.',
            'cost.value.min' => 'O campo valor deve ser maior ou igual a 0.',
            'cost.date.required' => 'O campo data é obrigatório.',
            'cost.date.date' => 'O campo data deve ser uma data válida.',
        ]);

        try {
            DB::beginTransaction();

            $CostCategory = CostCategory::firstOrCreate(
                ['id' => $this->cost['category_id']],
                ['name' => $this->cost['category_id']]
            );

            $this->cost['category_id'] = $CostCategory->id;

            Cost::create($this->cost);

            $this->cost = [
                'category_id' => null,
                'description' => null,
                'value' => null,
                'date' => null,
            ];
            
            $this->costCategories = CostCategory::all();
            $this->dispatch('costCadegorySelect2', costCategories: $this->costCategories);


            $this->costCollapseOpen = false;

            DB::commit();

            session()->flash('success', 'Custo salvo com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erro ao salvar o custo: ' . $e->getMessage());
        }
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
        $cities = DB::table('companies')->select('city')->where('active', true)->distinct()->get();
        
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
        $companies = DB::table('companies')->where('active', true)->get();
        
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

    public function costsTable()
    {
        $this->costs_array = [];
    
        // Garantindo que start e end cubram o dia completo
        $start = Carbon::parse($this->start)->startOfDay();
        $end = Carbon::parse($this->end)->endOfDay();
    
        // Custos diretos da tabela `costs`
        $directCosts = DB::table('costs')
            ->join('cost_categories', 'costs.category_id', '=', 'cost_categories.id')
            ->whereBetween('costs.date', [$start, $end])
            ->select('cost_categories.name as name', DB::raw('SUM(costs.value) as total'))
            ->groupBy('cost_categories.name')
            ->get();
    
        foreach ($directCosts as $cost) {
            $this->costs_array[$cost->name] = ($this->costs_array[$cost->name] ?? 0) + $cost->total;
        }
    
        // Custos embutidos na daily_rate
        $dailyRates = DB::table('daily_rate')
            ->whereBetween('start', [$start, $end])
            ->where('active', true)
            ->get();
    
        foreach ($dailyRates as $rate) {
            $this->costs_array['Comissões de líder'] = ($this->costs_array['Comissões de líder'] ?? 0) + $rate->leader_comission;
            $this->costs_array['Alimentação'] = ($this->costs_array['Alimentação'] ?? 0) + $rate->feeding;
            $pagamento = $rate->pay_amount - $rate->feeding;
            $this->costs_array['Pagamento'] = ($this->costs_array['Pagamento'] ?? 0) + $pagamento;
            $this->costs_array['Transporte'] = ($this->costs_array['Transporte'] ?? 0) + $rate->transportation;
            $this->costs_array['INSS'] = ($this->costs_array['INSS'] ?? 0) + $rate->inss_paid;
    
            $base = $rate->earned + $rate->addition;
            $imposto = $base * ($rate->tax_paid / 100);
            $this->costs_array['Imposto'] = ($this->costs_array['Imposto'] ?? 0) + $imposto;
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
        $costs_array = [];
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

        $registeredCosts = DB::table('costs')
        ->whereBetween('date', [
            Carbon::parse($this->start)->startOfDay()->toDateString(),
            Carbon::parse($this->end)->endOfDay()->toDateString()
        ])->sum('value');

        $this->total_costs += $registeredCosts;
        //$this->costsTable();
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
