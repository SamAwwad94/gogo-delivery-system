<aside class="fixed left-0 top-14 z-30 hidden h-[calc(100vh-3.5rem)] w-64 border-r border-border/40 bg-background lg:block">
    <div class="h-full overflow-y-auto py-4 px-3">
        <nav class="space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <i class="fas fa-tachometer-alt w-5 h-5"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Orders -->
            <div data-accordion-item>
                <button type="button" data-accordion-trigger class="flex w-full items-center justify-between rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('order.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}" aria-expanded="{{ request()->routeIs('order.*') ? 'true' : 'false' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-shopping-cart w-5 h-5"></i>
                        <span>Orders</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 h-4 transition-transform {{ request()->routeIs('order.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div data-accordion-content class="pl-8 pr-2 overflow-hidden transition-all">
                    <a href="{{ route('order.index') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('order.index') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>All Orders</span>
                    </a>
                    <a href="{{ route('order.create') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('order.create') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>Create Order</span>
                    </a>
                    <a href="{{ route('order.pending') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('order.pending') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>Pending Orders</span>
                    </a>
                </div>
            </div>
            
            <!-- Clients -->
            <a href="{{ route('client.index') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('client.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <i class="fas fa-users w-5 h-5"></i>
                <span>Clients</span>
            </a>
            
            <!-- Delivery Men -->
            <a href="{{ route('deliveryman.index') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('deliveryman.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <i class="fas fa-truck w-5 h-5"></i>
                <span>Delivery Men</span>
            </a>
            
            <!-- Vehicles -->
            <a href="{{ route('vehicle.index') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('vehicle.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <i class="fas fa-car w-5 h-5"></i>
                <span>Vehicles</span>
            </a>
            
            <!-- Delivery Routes -->
            <a href="{{ route('delivery-routes.index') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('delivery-routes.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <i class="fas fa-route w-5 h-5"></i>
                <span>Delivery Routes</span>
            </a>
            
            <!-- Locations -->
            <div data-accordion-item>
                <button type="button" data-accordion-trigger class="flex w-full items-center justify-between rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('country.*') || request()->routeIs('city.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}" aria-expanded="{{ request()->routeIs('country.*') || request()->routeIs('city.*') ? 'true' : 'false' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-map-marker-alt w-5 h-5"></i>
                        <span>Locations</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 h-4 transition-transform {{ request()->routeIs('country.*') || request()->routeIs('city.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div data-accordion-content class="pl-8 pr-2 overflow-hidden transition-all">
                    <a href="{{ route('country.index') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('country.*') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>Countries</span>
                    </a>
                    <a href="{{ route('city.index') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('city.*') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>Cities</span>
                    </a>
                </div>
            </div>
            
            <!-- Payments -->
            <a href="{{ route('payment.index') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('payment.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <i class="fas fa-credit-card w-5 h-5"></i>
                <span>Payments</span>
            </a>
            
            <!-- Settings -->
            <div data-accordion-item>
                <button type="button" data-accordion-trigger class="flex w-full items-center justify-between rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('setting.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}" aria-expanded="{{ request()->routeIs('setting.*') ? 'true' : 'false' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-cog w-5 h-5"></i>
                        <span>Settings</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 h-4 transition-transform {{ request()->routeIs('setting.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div data-accordion-content class="pl-8 pr-2 overflow-hidden transition-all">
                    <a href="{{ route('setting.index') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('setting.index') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>General</span>
                    </a>
                    <a href="{{ route('setting.app-settings') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('setting.app-settings') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>App Settings</span>
                    </a>
                    <a href="{{ route('setting.payment-settings') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('setting.payment-settings') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>Payment Settings</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
</aside>

<!-- Mobile sidebar -->
<div class="fixed inset-0 z-40 hidden bg-background/80 backdrop-blur-sm transition-all lg:hidden" id="mobile-sidebar-backdrop"></div>
<aside class="fixed left-0 top-14 z-50 h-[calc(100vh-3.5rem)] w-64 -translate-x-full border-r border-border/40 bg-background transition-transform lg:hidden" id="mobile-sidebar">
    <div class="h-full overflow-y-auto py-4 px-3">
        <nav class="space-y-1">
            <!-- Same navigation items as desktop sidebar -->
            <!-- Dashboard -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('home') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <i class="fas fa-tachometer-alt w-5 h-5"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Orders -->
            <div data-accordion-item>
                <button type="button" data-accordion-trigger class="flex w-full items-center justify-between rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('order.*') ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}" aria-expanded="{{ request()->routeIs('order.*') ? 'true' : 'false' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-shopping-cart w-5 h-5"></i>
                        <span>Orders</span>
                    </div>
                    <i class="fas fa-chevron-down w-4 h-4 transition-transform {{ request()->routeIs('order.*') ? 'rotate-180' : '' }}"></i>
                </button>
                <div data-accordion-content class="pl-8 pr-2 overflow-hidden transition-all">
                    <a href="{{ route('order.index') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('order.index') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>All Orders</span>
                    </a>
                    <a href="{{ route('order.create') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('order.create') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>Create Order</span>
                    </a>
                    <a href="{{ route('order.pending') }}" class="flex items-center space-x-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('order.pending') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                        <span>Pending Orders</span>
                    </a>
                </div>
            </div>
            
            <!-- Other menu items... -->
        </nav>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const mobileSidebarBackdrop = document.getElementById('mobile-sidebar-backdrop');
        
        if (mobileMenuButton && mobileSidebar && mobileSidebarBackdrop) {
            mobileMenuButton.addEventListener('click', function() {
                mobileSidebar.classList.toggle('-translate-x-full');
                mobileSidebarBackdrop.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            });
            
            mobileSidebarBackdrop.addEventListener('click', function() {
                mobileSidebar.classList.add('-translate-x-full');
                mobileSidebarBackdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        }
    });
</script>
