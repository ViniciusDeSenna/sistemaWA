<div>
    <button wire:click="$set('showModal', true)" class="btn btn-primary">
        + Adicionar Custo
    </button>

    @if($showModal)
        <div class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex justify-content-center align-items-center">
            <div class="bg-white p-4 rounded w-100 border" style="max-width: 400px;">
                <h2 class="text-center text-primary mb-3">Adicionar Custo</h2>

                <div class="mb-3">
                    <label class="form-label">Data</label>
                    <input type="date" wire:model="date" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nome do Custo</label>
                    <input type="text" wire:model="name" class="form-control" placeholder="Digite um nome">
                    <ul class="list-group mt-2">
                        @foreach($suggestedCosts as $suggestion)
                            <li wire:click="$set('name', '{{ $suggestion }}')" class="list-group-item list-group-item-action">
                                {{ $suggestion }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mb-3">
                    <label class="form-label">Valor</label>
                    <input type="number" wire:model="value" class="form-control" placeholder="Digite o valor">
                </div>

                <div class="d-flex gap-2">
                    <button wire:click="save" class="btn btn-success w-100">Salvar</button>
                    <button wire:click="$set('showModal', false)" class="btn btn-secondary w-100">Fechar</button>
                </div>

            </div>
        </div>
        
    @endif
</div>
