@props(['type' => 'text', 'class' => '', 'disabled' => false])

<input 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => 'shadcn-input ' . $class]) }}
    @if($disabled) disabled @endif
>
