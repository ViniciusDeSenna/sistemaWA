@props([
    'title' => null,
])

<div class="card">

    @if ($title)
        <h5 class="card-header">Basic</h5> 
    @endif
    
    <div class="card-body">
        {{ $slot }}
    </div>
    
</div>