@props(['class' => '', 'sortable' => false, 'sorted' => null])

<th {{ $attributes->merge(['class' => 'shadcn-table-head ' . $class]) }}>
    <div class="flex items-center space-x-1">
        <span>{{ $slot }}</span>
        @if($sortable)
            <button class="inline-flex">
                @if($sorted === 'asc')
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-up h-4 w-4"><path d="m18 15-6-6-6 6"/></svg>
                @elseif($sorted === 'desc')
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevrons-up-down h-4 w-4 opacity-50"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
                @endif
            </button>
        @endif
    </div>
</th>
