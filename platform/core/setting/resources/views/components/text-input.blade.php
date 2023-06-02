@props([
    'name',
    'label' => null,
    'type' => 'text',
    'helperText' => null,
])

<x-core-setting::form-group>
    @if($label)
        <label for="{{ $name }}" class="text-title-field">{{ $label }}</label>
    @endif

    <input {{ $attributes->merge(['type' => $type, 'class' => 'next-input', 'name' => $name, 'id' => $name]) }} value="{{ old($name) }}" type="{{ $type }}">

    @if($helperText)
        {{ Form::helper($helperText) }}
    @endif

    {{ $slot }}
</x-core-setting::form-group>
