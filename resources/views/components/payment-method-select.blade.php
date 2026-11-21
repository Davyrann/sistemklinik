@props([
    'name' => 'metode',
    'id' => 'metode',
    'selected' => null,
    'options' => \App\Models\Pembayaran::METODE,
])

@php
    $selectedVal = $selected ?? old($name, 'tunai');
@endphp

<x-select-input :id="$id" :name="$name" {{ $attributes }}>
    @foreach ($options as $val => $lbl)
        <option value="{{ $val }}" @selected((string) $selectedVal === (string) $val)>{{ $lbl }}</option>
    @endforeach
</x-select-input>
