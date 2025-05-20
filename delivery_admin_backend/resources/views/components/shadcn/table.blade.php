@props(['id' => null, 'class' => ''])

<div {{ $attributes->merge(['class' => 'shadcn-table-container rounded-md border']) }}>
    <table @if($id) id="{{ $id }}" @endif class="shadcn-table {{ $class }}">
        {{ $slot }}
    </table>
</div>
