<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Custo;
use App\Models\CustoRegisters; // Confirme que este é o nome correto do modelo
use Carbon\Carbon;

class AddCost extends Component
{
    public $showModal = false;
    public $date;
    public $name;
    public $value;
    public $suggestedCosts = [];

    protected $listeners = ['showAddCostModal' => 'showModal'];

    public function showModal()
    {
        $this->showModal = true;
    }

    public function updatedName()
    {
        $this->suggestedCosts = Custo::where('name', 'like', "%{$this->name}%")->pluck('name')->toArray();
    }

    public function save()
    {
        $this->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'value' => 'required|numeric|min:0',
        ]);

        $custo = Custo::firstOrCreate(['name' => $this->name]);

        // Criando o registro corretamente na tabela custo_registers
        CustoRegisters::create([
            'custo_id' => $custo->id,
            'date' => Carbon::parse($this->date),
            'value' => $this->value,
            'description' => '',
        ]);

        $this->reset(['date', 'name', 'value', 'showModal', 'suggestedCosts']);
        $this->dispatch('costAdded');
    }

    public function render()
    {
        return view('livewire.add-cost');
    }
}
