@props([
    'id',
    'name',
    'type',
    'label',
    'value' => null,
    'class' => null,
    'placeholder' => null
])

<div class="mb-3">
    <label class="form-label" for="{{ $id }}">{{ $label }}</label>
    <input 
        id="{{ $id }}" 
        type="{{ $type }}" 
        name="{{ $name }}" 
        class="form-control {{ $class }}" 
        placeholder="{{ $placeholder }}"
        value="{{ $value }}"
    />
</div>