@props(['class' => ''])

<caption {{ $attributes->merge(['class' => 'shadcn-table-caption ' . $class]) }}>
    {{ $slot }}
</caption>
