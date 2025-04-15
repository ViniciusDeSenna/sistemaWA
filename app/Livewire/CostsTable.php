<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Cost;
use Carbon\Carbon;

class CostsTable extends DataTableComponent
{
    protected $model = Cost::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()
                ->collapseOnMobile(),
            Column::make("Category id", "category_id")
                ->sortable()
                ->collapseOnMobile(),
            Column::make("Description", "description")
                ->sortable(),
            Column::make("Value", "value")
                ->sortable()
                ->collapseOnMobile(),
            Column::make("Date", "date")
                ->sortable()
                ->collapseOnMobile(),
            Column::make("Cadastro", "created_at")
                ->sortable()
                ->format(fn($value) => Carbon::parse($value)->format('d/m/Y'))
                ->collapseOnMobile(),
            Column::make('Ações')
                ->label(fn ($row) => view('components.fast-roundedbutton', [
                    'icon' => 'bx bx-trash',
                    'type' => 'danger',
                    'wire' => 'excluir(' . $row->id . ')',
                ]))
                ->html(),
            
        ];
    }

    public function excluir($id)
    {
        dd("Editar registro com ID: " . $id);
    }
}
