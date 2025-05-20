@props(['selected' => false, 'class' => ''])

<tr {{ $attributes->merge(['class' => 'shadcn-table-row ' . ($selected ? 'data-[state=selected]' : '') . ' ' . $class]) }}>
    {{ $slot }}
</tr>
