{{-- Alert Component --}}

@props(['type' => 'info', 'dismissible' => false, 'icon' => null, 'fade' => false])

<div class="alert alert-{{ $type }} {{ $dismissible ? 'alert-dismissible fade show' : '' }} {{ $fade ? 'auto-fade' : '' }}" role="alert" aria-live="assertive" aria-atomic="true">

    @if ($icon)
        <i class="{{ $icon }}"></i>
    @endif

    {{ $slot}}

    @if ($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', () => {
        const fadeOut = () => {
            $('.alert.auto-fade').fadeTo(2000, 500).slideUp(200, function () {
                $(this).alert('close');
            });
        };

        // Primeira renderização
        fadeOut();

        // Atualizações via Livewire
        Livewire.hook('message.processed', () => {
            fadeOut();
        });
    });
</script>
@endpush