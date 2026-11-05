@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-text']) }}>
    {{ $value ?? $slot }}@if($required)<span class="text-danger font-normal ml-0.5">(wajib)</span>@endif
</label>
