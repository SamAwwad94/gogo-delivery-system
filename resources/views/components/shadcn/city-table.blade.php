@props(['cityData'])

<div class="rounded-md border">
    <div class="relative w-full overflow-auto">
        <table class="w-full caption-bottom text-sm">
            <thead class="[&_tr]:border-b">
                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">City</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Total</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Delivered</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Cancelled</th>
                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">In Progress</th>
                </tr>
            </thead>
            <tbody class="[&_tr:last-child]:border-0">
                @foreach($cityData as $city)
                    <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                        <td class="p-4 align-middle">{{ $city['city'] }}</td>
                        <td class="p-4 align-middle">{{ $city['count'] }}</td>
                        <td class="p-4 align-middle">
                            <span
                                class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                {{ $city['delivered'] }}
                            </span>
                        </td>
                        <td class="p-4 align-middle">
                            <span
                                class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                {{ $city['cancelled'] }}
                            </span>
                        </td>
                        <td class="p-4 align-middle">
                            <span
                                class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                {{ $city['in_progress'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>