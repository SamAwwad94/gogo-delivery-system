<x-master-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/shadcn-table.css') }}">
        <link rel="stylesheet" href="{{ asset('css/new-orders-shadcn.css') }}">
        <link rel="stylesheet" href="{{ asset('css/shadcn-filter.css') }}">
    @endpush
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header Section -->
                        <div class="mb-6">
                            <h1 class="text-xl font-bold mb-2">Orders Management</h1>
                            <p class="text-gray-500">Manage your orders with modern components</p>
                        </div>

                        <!-- Summary Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <!-- Total Orders -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Total Orders</p>
                                        <h3 class="text-2xl font-bold">1,248</h3>
                                    </div>
                                    <div class="p-3 rounded-full bg-blue-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="text-blue-500">
                                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                                            <path d="M3 6h18"></path>
                                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-green-600 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="mr-1">
                                        <path d="m18 15-6-6-6 6"></path>
                                    </svg>
                                    <span>12.5% increase</span>
                                </div>
                            </div>

                            <!-- Pending Orders -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Pending Orders</p>
                                        <h3 class="text-2xl font-bold">42</h3>
                                    </div>
                                    <div class="p-3 rounded-full bg-yellow-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="text-yellow-500">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M12 6v6l4 2"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="mr-1">
                                        <path d="m6 9 6 6 6-6"></path>
                                    </svg>
                                    <span>3.2% increase</span>
                                </div>
                            </div>

                            <!-- Delivered Orders -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Delivered Orders</p>
                                        <h3 class="text-2xl font-bold">968</h3>
                                    </div>
                                    <div class="p-3 rounded-full bg-green-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                                            <path d="M20 6 9 17l-5-5"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-green-600 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="mr-1">
                                        <path d="m18 15-6-6-6 6"></path>
                                    </svg>
                                    <span>8.1% increase</span>
                                </div>
                            </div>

                            <!-- Revenue -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Total Revenue</p>
                                        <h3 class="text-2xl font-bold">$24,568</h3>
                                    </div>
                                    <div class="p-3 rounded-full bg-purple-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="text-purple-500">
                                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-green-600 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="mr-1">
                                        <path d="m18 15-6-6-6 6"></path>
                                    </svg>
                                    <span>15.3% increase</span>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Bar -->
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                            <!-- Quick Search -->
                            <div class="w-full mb-4 relative">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <path d="m21 21-4.3-4.3"></path>
                                        </svg>
                                    </div>
                                    <input type="search" id="quick-search"
                                        class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Search for orders, customers, phone numbers...">
                                </div>
                            </div>

                            <!-- Left Side: Filters -->
                            <div class="flex gap-3 items-center flex-wrap">
                                <!-- Order ID Filter -->
                                <div class="relative">
                                    <button type="button"
                                        class="shadcn-button shadcn-button-outline h-9 px-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="3" rx="2" />
                                            <path d="M7 7h10" />
                                            <path d="M7 12h10" />
                                            <path d="M7 17h10" />
                                        </svg>
                                        <span>Order ID</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                            <path d="m6 9 6 6 6-6" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Status Filter -->
                                <div class="relative">
                                    <button type="button"
                                        class="shadcn-button shadcn-button-outline h-9 px-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                                        </svg>
                                        <span>Status</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                            <path d="m6 9 6 6 6-6" />
                                        </svg>
                                    </button>
                                    <div
                                        class="filter-dropdown hidden absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10 p-2">
                                        <div class="p-2">
                                            <div class="max-h-48 overflow-y-auto">
                                                <div
                                                    class="flex items-center px-3 py-2 text-sm rounded-md hover:bg-gray-100 cursor-pointer">
                                                    <input type="checkbox" class="mr-2 rounded border-gray-300">
                                                    <span
                                                        class="status-pill bg-yellow-100 text-yellow-800 ml-2">Pending</span>
                                                </div>
                                                <div
                                                    class="flex items-center px-3 py-2 text-sm rounded-md hover:bg-gray-100 cursor-pointer">
                                                    <input type="checkbox" class="mr-2 rounded border-gray-300">
                                                    <span
                                                        class="status-pill bg-blue-100 text-blue-800 ml-2">Processing</span>
                                                </div>
                                                <div
                                                    class="flex items-center px-3 py-2 text-sm rounded-md hover:bg-gray-100 cursor-pointer">
                                                    <input type="checkbox" class="mr-2 rounded border-gray-300">
                                                    <span
                                                        class="status-pill bg-purple-100 text-purple-800 ml-2">Shipped</span>
                                                </div>
                                                <div
                                                    class="flex items-center px-3 py-2 text-sm rounded-md hover:bg-gray-100 cursor-pointer">
                                                    <input type="checkbox" class="mr-2 rounded border-gray-300">
                                                    <span
                                                        class="status-pill bg-green-100 text-green-800 ml-2">Delivered</span>
                                                </div>
                                                <div
                                                    class="flex items-center px-3 py-2 text-sm rounded-md hover:bg-gray-100 cursor-pointer">
                                                    <input type="checkbox" class="mr-2 rounded border-gray-300">
                                                    <span
                                                        class="status-pill bg-red-100 text-red-800 ml-2">Cancelled</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Filter -->
                                <div class="relative">
                                    <button type="button"
                                        class="shadcn-button shadcn-button-outline h-9 px-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        <span>Customer</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                            <path d="m6 9 6 6 6-6" />
                                        </svg>
                                    </button>
                                    <div
                                        class="filter-dropdown hidden absolute left-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10 p-2">
                                        <div class="p-2">
                                            <input type="text" class="shadcn-input h-9 w-full mb-2"
                                                placeholder="Search customers...">
                                            <div class="max-h-48 overflow-y-auto">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <div
                                                        class="flex items-center px-3 py-2 text-sm rounded-md hover:bg-gray-100 cursor-pointer">
                                                        <input type="checkbox" class="mr-2 rounded border-gray-300">
                                                        <div class="flex items-center">
                                                            <div
                                                                class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                                                <span class="text-xs font-medium">{{ chr(64 + $i) }}</span>
                                                            </div>
                                                            <span>Customer {{ $i }}</span>
                                                        </div>
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Phone Filter -->
                                <div class="relative">
                                    <button type="button"
                                        class="shadcn-button shadcn-button-outline h-9 px-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                        </svg>
                                        <span>Phone</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                            <path d="m6 9 6 6 6-6" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Locations Filter -->
                                <div class="relative">
                                    <button type="button"
                                        class="shadcn-button shadcn-button-outline h-9 px-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        <span>Locations</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                            <path d="m6 9 6 6 6-6" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Payment Status Filter -->
                                <div class="relative">
                                    <button type="button"
                                        class="shadcn-button shadcn-button-outline h-9 px-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="20" height="14" x="2" y="5" rx="2" />
                                            <line x1="2" x2="22" y1="10" y2="10" />
                                        </svg>
                                        <span>Payment</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                            <path d="m6 9 6 6 6-6" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Date Range Filter -->
                                <div class="relative">
                                    <button type="button"
                                        class="shadcn-button shadcn-button-outline h-9 px-4 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                            <line x1="16" x2="16" y1="2" y2="6" />
                                            <line x1="8" x2="8" y1="2" y2="6" />
                                            <line x1="3" x2="21" y1="10" y2="10" />
                                        </svg>
                                        <span>Date Range</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="ml-2">
                                            <path d="m6 9 6 6 6-6" />
                                        </svg>
                                    </button>
                                    <div
                                        class="filter-dropdown hidden absolute left-0 mt-2 w-72 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10 p-2">
                                        <div class="p-2">
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Start
                                                    Date</label>
                                                <input type="date" class="shadcn-input h-9 w-full">
                                            </div>
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">End
                                                    Date</label>
                                                <input type="date" class="shadcn-input h-9 w-full">
                                            </div>
                                            <div class="flex justify-between">
                                                <button
                                                    class="shadcn-button shadcn-button-outline h-9 px-4">Reset</button>
                                                <button
                                                    class="shadcn-button shadcn-button-primary h-9 px-4">Apply</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side: Action Buttons -->
                            <div class="flex gap-2">
                                <button type="button"
                                    class="shadcn-button shadcn-button-primary h-9 px-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M5 12h14" />
                                        <path d="M12 5v14" />
                                    </svg>
                                    <span>New Order</span>
                                </button>
                                <button type="button"
                                    class="shadcn-button shadcn-button-outline h-9 px-4 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                        <path d="M3 3v5h5" />
                                        <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                                        <path d="M16 21h5v-5" />
                                    </svg>
                                    <span>Refresh</span>
                                </button>
                            </div>
                        </div>

                        <!-- Tabs for Shipping Method -->
                        <div class="mb-6 border-b border-gray-200">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                                <li class="mr-2">
                                    <a href="#"
                                        class="tab-link inline-block p-4 border-b-2 border-primary-500 text-primary-600 rounded-t-lg active">All</a>
                                </li>
                                <li class="mr-2">
                                    <a href="#"
                                        class="tab-link inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">Dallas
                                        Express</a>
                                </li>
                                <li class="mr-2">
                                    <a href="#"
                                        class="tab-link inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">Express
                                        (Auto-pilot)</a>
                                </li>
                                <li class="mr-2">
                                    <a href="#"
                                        class="tab-link inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">New</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="tab-link inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">Today</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Orders Table -->
                        <div class="mt-6">
                            <div class="shadcn-table-container">
                                <table id="orders-table" class="shadcn-table">
                                    <thead>
                                        <tr>
                                            <th class="shadcn-table-head">
                                                <div class="flex items-center">
                                                    <input type="checkbox" class="shadcn-checkbox">
                                                </div>
                                            </th>
                                            <th class="shadcn-table-head">Order ID</th>
                                            <th class="shadcn-table-head">Customer</th>
                                            <th class="shadcn-table-head">Status</th>
                                            <th class="shadcn-table-head">Date</th>
                                            <th class="shadcn-table-head">Total</th>
                                            <th class="shadcn-table-head">Payment</th>
                                            <th class="shadcn-table-head">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 1; $i <= 10; $i++)
                                            <tr class="shadcn-table-row">
                                                <td class="shadcn-table-cell">
                                                    <div class="flex items-center">
                                                        <input type="checkbox" class="shadcn-checkbox">
                                                    </div>
                                                </td>
                                                <td class="shadcn-table-cell font-medium">#ORD-{{ 1000 + $i }}</td>
                                                <td class="shadcn-table-cell">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                                                            <span class="text-xs font-medium">{{ chr(64 + $i) }}</span>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium">Customer {{ $i }}</div>
                                                            <div class="text-sm text-gray-500">customer{{ $i }}@example.com
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="shadcn-table-cell">
                                                    @php
                                                        $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
                                                        $statusClasses = [
                                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                                            'Processing' => 'bg-blue-100 text-blue-800',
                                                            'Shipped' => 'bg-purple-100 text-purple-800',
                                                            'Delivered' => 'bg-green-100 text-green-800',
                                                            'Cancelled' => 'bg-red-100 text-red-800'
                                                        ];
                                                        $status = $statuses[$i % count($statuses)];
                                                    @endphp
                                                    <span class="status-pill {{ $statusClasses[$status] }}">
                                                        {{ $status }}
                                                    </span>
                                                </td>
                                                <td class="shadcn-table-cell">{{ date('M d, Y', strtotime("-{$i} days")) }}
                                                </td>
                                                <td class="shadcn-table-cell">${{ rand(50, 500) }}.00</td>
                                                <td class="shadcn-table-cell">
                                                    @php
                                                        $payments = ['Paid', 'Unpaid', 'Refunded'];
                                                        $paymentClasses = [
                                                            'Paid' => 'bg-green-100 text-green-800',
                                                            'Unpaid' => 'bg-yellow-100 text-yellow-800',
                                                            'Refunded' => 'bg-gray-100 text-gray-800'
                                                        ];
                                                        $payment = $payments[$i % count($payments)];
                                                    @endphp
                                                    <span class="status-pill {{ $paymentClasses[$payment] }}">
                                                        {{ $payment }}
                                                    </span>
                                                </td>
                                                <td class="shadcn-table-cell">
                                                    <div class="flex space-x-2">
                                                        <button class="shadcn-button shadcn-button-sm shadcn-button-ghost">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" class="text-blue-500">
                                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z">
                                                                </path>
                                                                <circle cx="12" cy="12" r="3"></circle>
                                                            </svg>
                                                        </button>
                                                        <button class="shadcn-button shadcn-button-sm shadcn-button-ghost">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" class="text-blue-500">
                                                                <path
                                                                    d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7">
                                                                </path>
                                                                <path
                                                                    d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                        <button class="shadcn-button shadcn-button-sm shadcn-button-ghost">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" class="text-red-500">
                                                                <path d="M3 6h18"></path>
                                                                <path
                                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex items-center justify-between mt-4">
                                <div class="text-sm text-muted-foreground">
                                    Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of
                                    <span class="font-medium">100</span> results
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button
                                        class="shadcn-button shadcn-button-outline h-8 w-8 p-0 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m15 18-6-6 6-6" />
                                        </svg>
                                    </button>
                                    <button
                                        class="shadcn-button h-8 min-w-[32px] bg-primary text-primary-foreground">1</button>
                                    <button class="shadcn-button shadcn-button-outline h-8 min-w-[32px]">2</button>
                                    <button class="shadcn-button shadcn-button-outline h-8 min-w-[32px]">3</button>
                                    <button class="shadcn-button shadcn-button-outline h-8 min-w-[32px]">...</button>
                                    <button class="shadcn-button shadcn-button-outline h-8 min-w-[32px]">10</button>
                                    <button
                                        class="shadcn-button shadcn-button-outline h-8 w-8 p-0 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m9 18 6-6-6-6" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/shadcn-table-transformer.js') }}"></script>
        <script>
            $(document).ready(function () {
                // Initialize ShadCN Table
                new ShadcnTableTransformer({
                    tableSelector: '#orders-table',
                    enableFiltering: true,
                    enableSorting: true,
                    enablePagination: true,
                    enableColumnVisibility: true
                });

                // Filter dropdown functionality
                $('.shadcn-button-outline').on('click', function () {
                    // Close all other dropdowns
                    $('.filter-dropdown').not($(this).next('.filter-dropdown')).addClass('hidden');

                    // Toggle this dropdown
                    $(this).next('.filter-dropdown').toggleClass('hidden');
                });

                // Close dropdowns when clicking outside
                $(document).on('click', function (e) {
                    if (!$(e.target).closest('.relative').length) {
                        $('.filter-dropdown').addClass('hidden');
                    }
                });

                // Tab functionality
                $('.tab-link').on('click', function (e) {
                    e.preventDefault();

                    // Remove active class from all tabs
                    $('.tab-link').removeClass('active border-primary-500 text-primary-600').addClass('border-transparent hover:text-gray-600 hover:border-gray-300');

                    // Add active class to clicked tab
                    $(this).addClass('active border-primary-500 text-primary-600').removeClass('border-transparent hover:text-gray-600 hover:border-gray-300');
                });

                // Quick search functionality
                $('#quick-search').on('keyup', function () {
                    const searchTerm = $(this).val().toLowerCase();

                    // If search term is empty, show all rows
                    if (searchTerm === '') {
                        $('.shadcn-table-row').show();
                        return;
                    }

                    // Hide/show rows based on search term
                    $('.shadcn-table-row').each(function () {
                        const rowText = $(this).text().toLowerCase();
                        if (rowText.includes(searchTerm)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-master-layout>