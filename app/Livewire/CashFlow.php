<?php

namespace App\Livewire;

use Livewire\Component;

class CashFlow extends Component
{
    public function render()
    {
        return view('livewire.cash-flow')->layout('layouts.app');
    }
}
