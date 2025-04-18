@props([
    'name' => null,
    'cardTitle' => null,
    'buttonTitle' => null,
    'buttonType' => 'primary',
    'buttonClick' => null,
])

<div class="col-12 col-md-2"> 
    <!-- Button trigger modal -->
    <button id="{{ $name . 'Button' }}" type="button" class="btn btn-{{ $buttonType }} w-100" data-bs-toggle="modal" data-bs-target="#{{ $name }}">
        {{ $buttonIcon ?? '' }}
        {{ $buttonTitle }}
    </button>
    
    <!-- Modal -->
    <div class="modal fade" id="{{ $name }}" tabindex="-1" aria-labelledby="{{ $name }}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="{{ $name }}Label">{{ $cardTitle }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{ $slot }}
                </div>

                @isset($footer)
                    <div class="modal-footer">
                        {{ $footer }}
                    </div> 
                @endisset

            </div>
        </div>
    </div>
</div>