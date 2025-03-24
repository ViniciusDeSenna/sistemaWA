@props([
    'id',
    'name',
    'label',
    'placeholder' => null
])

<div class="mb-3">
    <label class="form-label" for="{{ $id }}">{{ $label }}</label>
    <textarea 
        id="{{ $id }}" 
        class="form-control" 
        placeholder="{{ $placeholder }}" 
        name="{{ $name }}"

    > {{ $slot }} </textarea>
  
</div>