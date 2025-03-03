<x-app-layout>

<div class="card-body">
    <form id="form-edit-jobs">
        <div class="mb-3">
            <label class="form-label" for="basic-default-fullname"></label>
            <input type="text" class="form-control" id="basic-default-fullname">
        </div>
        <div>
        <input class="form-control" list="employees-list" name="employee">
        <datalist id="employees-list">
            @foreach ($colaborators as $colaborator)
                <option value="{{ $colaborator->name}}">

            @endforeach
        </div>
        <div>
        <input class="form-control" list="companies-list" name="companies">
        <datalist id="companies-list">
            @foreach ($establishments as $establishment)
                <option value="{{ $establishment->name}}">

            @endforeach
        </div>
    </form>



</div>


<div class="container d-flex justify-content-start">
    <button type="button" class="btn btn-primary right" style="margin-right: 51%" onclick="window.history.back();">Voltar</button>   
    @if ($establishment?->id ?? false)
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary right" style="margin-right: 0%" onclick="update({{ $establishment?->id ?? null }})">Salvar</button>
        </div>
    @else
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-primary right" style="margin-right: 0%"onclick="post()">Salvar</button>
        </div>
    @endif
</div>

</x-app-layout>