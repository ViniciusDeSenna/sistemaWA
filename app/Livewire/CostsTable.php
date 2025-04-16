<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Cost;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class CostsTable extends DataTableComponent
{
    protected $model = Cost::class;
    public ?string $start = null;
    public ?string $end = null;

    protected $listeners = ['refreshCostsTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function mount(?string $start = null, ?string $end = null)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function filters(): array
    {
        return [
            DateRangeFilter::make('Data')
                ->config([
                    'allowInput' => true,
                    'altFormat' => 'j/F/Y',
                    'ariaDateFormat' => 'j/F/Y',
                    'dateFormat' => 'd/m/Y',
                    'placeholder' => 'Insira um periodo',
                    'locale' => 'ptbr',
                ])
                ->setFilterPillValues([0 => 'minDate', 1 => 'maxDate']) // The values that will be displayed for the Min/Max Date Values
                ->filter(function (Builder $builder, array $dateRange) { // Expects an array.
                    if (isset($dateRange['minDate']) && $dateRange['minDate'] !== null) {
                        $builder->whereDate('date', '>=', $dateRange['minDate']); // minDate is the start date selected
                    }

                    if (isset($dateRange['maxDate']) && $dateRange['maxDate'] !== null) {
                        $builder->whereDate('date', '<=', $dateRange['maxDate']); // maxDate is the end date selected
                    }
                })
                ->setFilterDefaultValue(['minDate' => $this->start, 'maxDate' => $this->end])
        ];
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()
                ->collapseOnMobile(),
            Column::make("Categoria", "category.name")
                ->sortable()
                ->collapseOnMobile(),
            Column::make("Description", "description")
                ->sortable(),
            Column::make("Value", "value")
                ->sortable()
                ->collapseOnMobile(),
            Column::make("Data", "date")
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
        $cost = Cost::find($id);
        if ($cost) {
            $cost->delete();
            session()->flash('success', 'Custo excluído com sucesso!');
            $this->resetPage();
        } else {
            session()->flash('error', 'Custo não encontrado!');
        }
    }
}
