@props([
    'icon' => 'bx bx-bell',
    'type' => 'primary'
])

<div>
    <button type="button" class="btn rounded-pill btn-icon btn-{{ $type }}" @if ($wire) wire:click="{{ $wire }}" @endif>
        <span class="tf-icons {{ $icon }}"></span>
    </button>
</div>