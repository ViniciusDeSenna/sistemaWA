<x-errors-layout>
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
            <h2 class="mb-2 mx-2">Permissão negada :(</h2>
            <p class="mb-4 mx-2">Oops! 😖 Parece que você não possui permissão para acessar essa página.</p>
            <a href="{{ url()->previous() }}" class="btn btn-primary">Voltar</a>
        </div>
    </div>
</x-errors-layout>