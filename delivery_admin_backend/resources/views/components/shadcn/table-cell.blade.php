@props(['class' => ''])

<td {{ $attributes->merge(['class' => 'shadcn-table-cell ' . $class]) }}>
    {{ $slot }}
</td>
