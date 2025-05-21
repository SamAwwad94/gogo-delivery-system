<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">ShadCN Table Example</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>This is an example of using the ShadCN table component with existing Laravel data.</p>
                        
                        <h5 class="mt-4 mb-3">Method 1: Using the Blade Component</h5>
                        
                        @php
                        $columns = [
                            ['id' => 'id', 'key' => 'id', 'label' => 'ID', 'sortable' => true, 'filterable' => true, 'type' => 'number'],
                            ['id' => 'name', 'key' => 'name', 'label' => 'Name', 'sortable' => true, 'filterable' => true],
                            ['id' => 'status', 'key' => 'status', 'label' => 'Status', 'sortable' => true, 'filterable' => true, 'type' => 'select', 
                             'options' => [
                                ['label' => 'Active', 'value' => 'active'],
                                ['label' => 'Inactive', 'value' => 'inactive'],
                                ['label' => 'Pending', 'value' => 'pending']
                             ]],
                            ['id' => 'created_at', 'key' => 'created_at', 'label' => 'Created At', 'sortable' => true, 'filterable' => true, 'type' => 'date'],
                            ['id' => 'actions', 'key' => 'actions', 'label' => 'Actions', 'sortable' => false, 'filterable' => false]
                        ];
                        
                        // Sample data - in a real application, this would come from your controller
                        $sampleData = [
                            ['id' => 1, 'name' => 'John Doe', 'status' => 'active', 'created_at' => '2023-01-15'],
                            ['id' => 2, 'name' => 'Jane Smith', 'status' => 'inactive', 'created_at' => '2023-02-20'],
                            ['id' => 3, 'name' => 'Bob Johnson', 'status' => 'pending', 'created_at' => '2023-03-10'],
                            ['id' => 4, 'name' => 'Alice Brown', 'status' => 'active', 'created_at' => '2023-04-05'],
                            ['id' => 5, 'name' => 'Charlie Wilson', 'status' => 'inactive', 'created_at' => '2023-05-12']
                        ];
                        @endphp
                        
                        <x-shadcn-table id="example-table" :columns="$columns">
                            @foreach($sampleData as $item)
                                <tr>
                                    <td>{{ $item['id'] }}</td>
                                    <td>{{ $item['name'] }}</td>
                                    <td>
                                        <span class="status-pill 
                                            @if($item['status'] == 'active') bg-green-100 text-green-800 @endif
                                            @if($item['status'] == 'inactive') bg-red-100 text-red-800 @endif
                                            @if($item['status'] == 'pending') bg-blue-100 text-blue-800 @endif
                                        ">
                                            {{ ucfirst($item['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ $item['created_at'] }}</td>
                                    <td>
                                        <div class="flex space-x-2">
                                            <button class="shadcn-button shadcn-button-sm shadcn-button-ghost">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                            </button>
                                            <button class="shadcn-button shadcn-button-sm shadcn-button-ghost">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </x-shadcn-table>
                        
                        <h5 class="mt-5 mb-3">Method 2: Transforming an Existing Table</h5>
                        <p>Add the class <code>transform-to-shadcn</code> to any existing table to transform it.</p>
                        
                        <table id="existing-table" class="table transform-to-shadcn">
                            <thead>
                                <tr>
                                    <th data-column-id="id" data-type="number">ID</th>
                                    <th data-column-id="name">Name</th>
                                    <th data-column-id="status" data-type="select" data-options='[{"label":"Active","value":"active"},{"label":"Inactive","value":"inactive"},{"label":"Pending","value":"pending"}]'>Status</th>
                                    <th data-column-id="created_at" data-type="date">Created At</th>
                                    <th data-column-id="actions" data-sortable="false" data-filterable="false">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sampleData as $item)
                                    <tr>
                                        <td>{{ $item['id'] }}</td>
                                        <td>{{ $item['name'] }}</td>
                                        <td>
                                            <span class="status-pill 
                                                @if($item['status'] == 'active') bg-green-100 text-green-800 @endif
                                                @if($item['status'] == 'inactive') bg-red-100 text-red-800 @endif
                                                @if($item['status'] == 'pending') bg-blue-100 text-blue-800 @endif
                                            ">
                                                {{ ucfirst($item['status']) }}
                                            </span>
                                        </td>
                                        <td>{{ $item['created_at'] }}</td>
                                        <td>
                                            <div class="flex space-x-2">
                                                <button class="shadcn-button shadcn-button-sm shadcn-button-ghost">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                                </button>
                                                <button class="shadcn-button shadcn-button-sm shadcn-button-ghost">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="{{ asset('js/shadcn-table-transformer.js') }}"></script>
    @endpush
</x-master-layout>
