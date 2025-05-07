@props([
    'name' => null,
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'type' => 'text',
    'required' => false,
    'disabled' => false,
    'readonly' => false,
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ $value }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} {{ $readonly ? 'readonly' : '' }} {{ $attributes->merge(['class' => 'form-control']) }}>
    @error($name)
        <div class="alert alert-danger mt-2">
            {{ $message }}
        </div>
    @enderror
</div>