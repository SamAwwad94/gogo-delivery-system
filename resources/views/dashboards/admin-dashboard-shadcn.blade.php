<x-shadcn-layout>
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
            <p class="text-muted-foreground">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative" data-dropdown>
                <button type="button" class="flex items-center space-x-1 rounded-md border border-input bg-background px-3 py-2 text-sm font-medium hover:bg-accent hover:text-accent-foreground" data-dropdown-trigger>
                    <span>{{ request('date_range', 'Today') }}</span>
                    <i class="fas fa-chevron-down w-4 h-4"></i>
                </button>
                <div class="absolute right-0 top-full mt-1 w-48 rounded-md border border-border bg-popover p-1 shadow-md" data-dropdown-content>
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground {{ request('date_range') == '' ? 'bg-accent text-accent-foreground' : '' }}">
                        <span>Today</span>
                    </a>
                    <a href="{{ route('home', ['date_range' => 'This Week']) }}" class="flex items-center space-x-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground {{ request('date_range') == 'This Week' ? 'bg-accent text-accent-foreground' : '' }}">
                        <span>This Week</span>
                    </a>
                    <a href="{{ route('home', ['date_range' => 'This Month']) }}" class="flex items-center space-x-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground {{ request('date_range') == 'This Month' ? 'bg-accent text-accent-foreground' : '' }}">
                        <span>This Month</span>
                    </a>
                    <a href="{{ route('home', ['date_range' => 'This Year']) }}" class="flex items-center space-x-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground {{ request('date_range') == 'This Year' ? 'bg-accent text-accent-foreground' : '' }}">
                        <span>This Year</span>
                    </a>
                </div>
            </div>
            <a href="{{ route('order.create') }}" class="shadcn-button shadcn-button-default">
                <i class="fas fa-plus mr-2"></i>
                Create Order
            </a>
        </div>
    </div>
    
    <!-- Stats cards -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Orders -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Total Orders</h3>
                <i class="fas fa-shopping-cart h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold">{{ $total_order }}</div>
                <p class="text-xs text-muted-foreground">
                    @if(isset($order_percentage) && $order_percentage > 0)
                        <span class="text-success">+{{ $order_percentage }}%</span> from previous period
                    @elseif(isset($order_percentage) && $order_percentage < 0)
                        <span class="text-destructive">{{ $order_percentage }}%</span> from previous period
                    @else
                        No change from previous period
                    @endif
                </p>
            </div>
        </div>
        
        <!-- Total Clients -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Total Clients</h3>
                <i class="fas fa-users h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold">{{ $total_client }}</div>
                <p class="text-xs text-muted-foreground">
                    @if(isset($client_percentage) && $client_percentage > 0)
                        <span class="text-success">+{{ $client_percentage }}%</span> from previous period
                    @elseif(isset($client_percentage) && $client_percentage < 0)
                        <span class="text-destructive">{{ $client_percentage }}%</span> from previous period
                    @else
                        No change from previous period
                    @endif
                </p>
            </div>
        </div>
        
        <!-- Total Delivery Men -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Total Delivery Men</h3>
                <i class="fas fa-truck h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold">{{ $total_delivery_man }}</div>
                <p class="text-xs text-muted-foreground">
                    @if(isset($delivery_man_percentage) && $delivery_man_percentage > 0)
                        <span class="text-success">+{{ $delivery_man_percentage }}%</span> from previous period
                    @elseif(isset($delivery_man_percentage) && $delivery_man_percentage < 0)
                        <span class="text-destructive">{{ $delivery_man_percentage }}%</span> from previous period
                    @else
                        No change from previous period
                    @endif
                </p>
            </div>
        </div>
        
        <!-- Total Revenue -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                <h3 class="tracking-tight text-sm font-medium">Total Revenue</h3>
                <i class="fas fa-dollar-sign h-4 w-4 text-muted-foreground"></i>
            </div>
            <div class="p-6 pt-0">
                <div class="text-2xl font-bold">{{ getPriceFormat($total_revenue) }}</div>
                <p class="text-xs text-muted-foreground">
                    @if(isset($revenue_percentage) && $revenue_percentage > 0)
                        <span class="text-success">+{{ $revenue_percentage }}%</span> from previous period
                    @elseif(isset($revenue_percentage) && $revenue_percentage < 0)
                        <span class="text-destructive">{{ $revenue_percentage }}%</span> from previous period
                    @else
                        No change from previous period
                    @endif
                </p>
            </div>
        </div>
    </div>
    
    <!-- Charts and tables -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7 mt-4">
        <!-- Order Statistics Chart -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm md:col-span-2 lg:col-span-4">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Order Statistics</h3>
                <p class="text-sm text-muted-foreground">Order trends over time</p>
            </div>
            <div class="p-6 pt-0">
                <div id="order-statistics-chart" class="h-80"></div>
            </div>
        </div>
        
        <!-- Order Status Chart -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm md:col-span-2 lg:col-span-3">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Order Status</h3>
                <p class="text-sm text-muted-foreground">Distribution of orders by status</p>
            </div>
            <div class="p-6 pt-0">
                <div id="order-status-chart" class="h-80"></div>
            </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm md:col-span-2 lg:col-span-4">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Recent Orders</h3>
                <p class="text-sm text-muted-foreground">Latest orders in the system</p>
            </div>
            <div class="p-6 pt-0">
                <div class="relative w-full overflow-auto">
                    <table class="w-full caption-bottom text-sm">
                        <thead>
                            <tr class="border-b transition-colors hover:bg-muted/50">
                                <th class="h-10 px-2 text-left align-middle font-medium text-muted-foreground">Order ID</th>
                                <th class="h-10 px-2 text-left align-middle font-medium text-muted-foreground">Client</th>
                                <th class="h-10 px-2 text-left align-middle font-medium text-muted-foreground">Status</th>
                                <th class="h-10 px-2 text-left align-middle font-medium text-muted-foreground">Amount</th>
                                <th class="h-10 px-2 text-left align-middle font-medium text-muted-foreground">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_orders as $order)
                                <tr class="border-b transition-colors hover:bg-muted/50">
                                    <td class="p-2 align-middle"><a href="{{ route('order.show', $order->id) }}" class="text-primary hover:underline">#{{ $order->id }}</a></td>
                                    <td class="p-2 align-middle">{{ optional($order->client)->name ?? 'N/A' }}</td>
                                    <td class="p-2 align-middle">
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                            @if($order->status == 'completed')
                                                bg-success/10 text-success ring-success/20
                                            @elseif($order->status == 'cancelled')
                                                bg-destructive/10 text-destructive ring-destructive/20
                                            @elseif($order->status == 'pending')
                                                bg-warning/10 text-warning ring-warning/20
                                            @else
                                                bg-primary/10 text-primary ring-primary/20
                                            @endif
                                        ">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="p-2 align-middle">{{ getPriceFormat($order->total_amount) }}</td>
                                    <td class="p-2 align-middle">{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-2 text-center text-muted-foreground">No recent orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('order.index') }}" class="text-sm text-primary hover:underline">View all orders</a>
                </div>
            </div>
        </div>
        
        <!-- Top Clients -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm md:col-span-2 lg:col-span-3">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-lg font-semibold leading-none tracking-tight">Top Clients</h3>
                <p class="text-sm text-muted-foreground">Clients with most orders</p>
            </div>
            <div class="p-6 pt-0">
                <div class="space-y-4">
                    @forelse($top_clients as $client)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ $client->name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $client->email }}</p>
                                </div>
                            </div>
                            <div class="text-sm font-medium">{{ $client->orders_count }} orders</div>
                        </div>
                    @empty
                        <div class="text-center text-muted-foreground">No clients found</div>
                    @endforelse
                </div>
                <div class="mt-4 flex justify-center">
                    <a href="{{ route('client.index') }}" class="text-sm text-primary hover:underline">View all clients</a>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Order Statistics Chart
            const orderStatisticsOptions = {
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    }
                },
                series: [{
                    name: 'Orders',
                    data: @json($order_chart_data['values'])
                }],
                xaxis: {
                    categories: @json($order_chart_data['labels']),
                    labels: {
                        style: {
                            colors: getComputedStyle(document.documentElement).getPropertyValue('--muted-foreground')
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: getComputedStyle(document.documentElement).getPropertyValue('--muted-foreground')
                        }
                    }
                },
                colors: [getComputedStyle(document.documentElement).getPropertyValue('--primary')],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                grid: {
                    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--border'),
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                }
            };
            
            const orderStatisticsChart = new ApexCharts(document.querySelector('#order-statistics-chart'), orderStatisticsOptions);
            orderStatisticsChart.render();
            
            // Order Status Chart
            const orderStatusOptions = {
                chart: {
                    type: 'donut',
                    height: 320,
                    toolbar: {
                        show: false
                    }
                },
                series: @json($order_status_chart_data['values']),
                labels: @json($order_status_chart_data['labels']),
                colors: ['#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6'],
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: getComputedStyle(document.documentElement).getPropertyValue('--foreground')
                    }
                },
                dataLabels: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '50%'
                        }
                    }
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                }
            };
            
            const orderStatusChart = new ApexCharts(document.querySelector('#order-status-chart'), orderStatusOptions);
            orderStatusChart.render();
            
            // Update charts when theme changes
            window.addEventListener('theme-changed', function(e) {
                const isDark = e.detail.theme === 'dark';
                
                orderStatisticsChart.updateOptions({
                    grid: {
                        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--border')
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light'
                    },
                    xaxis: {
                        labels: {
                            style: {
                                colors: getComputedStyle(document.documentElement).getPropertyValue('--muted-foreground')
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: getComputedStyle(document.documentElement).getPropertyValue('--muted-foreground')
                            }
                        }
                    }
                });
                
                orderStatusChart.updateOptions({
                    legend: {
                        labels: {
                            colors: getComputedStyle(document.documentElement).getPropertyValue('--foreground')
                        }
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light'
                    }
                });
            });
        });
    </script>
    @endpush
</x-shadcn-layout>
