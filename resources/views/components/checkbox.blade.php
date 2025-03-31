@props([
    'id',
    'name',
    'title',
    'checked' => false,  
])

<div class="form-check mt-3">
    <input class="form-check-input" type="checkbox" name="{{ $name }}" id="{{ $id }}" @checked($checked)>
    <label class="form-check-label" for="{{ $id }}"> {{ $title }} </label>
</div>