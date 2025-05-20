@props([
    'type' => 'button',
    'variant' => 'default',
    'size' => 'default',
    'class' => '',
    'disabled' => false
])

@php
    $baseClass = 'shadcn-button';
    $variantClass = match($variant) {
        'default' => 'shadcn-button-default',
        'destructive' => 'shadcn-button-destructive',
        'outline' => 'shadcn-button-outline',
        'secondary' => 'shadcn-button-secondary',
        'ghost' => 'shadcn-button-ghost',
        'link' => 'shadcn-button-link',
        default => 'shadcn-button-default'
    };
    
    $sizeClass = match($size) {
        'default' => 'h-9 px-4 py-2',
        'sm' => 'h-8 rounded-md px-3 text-xs',
        'lg' => 'h-10 rounded-md px-8',
        'icon' => 'h-9 w-9',
        default => 'h-9 px-4 py-2'
    };
@endphp

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => "{$baseClass} {$variantClass} {$sizeClass} {$class}"]) }}
    @if($disabled) disabled @endif
>
    {{ $slot }}
</button>
