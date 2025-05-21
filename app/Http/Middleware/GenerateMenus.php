<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Menu;

class GenerateMenus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Add ShadCN demo to the sidebar menu
        if (Auth::check() && auth()->user()->user_type == 'admin') {
            Menu::make('MyNavBar', function ($menu) {
                // Add ShadCN UI Demo section
                $menu->add('<span>ShadCN UI Demo</span>', ['class' => ''])
                    ->prepend('<i class="fas fa-table"></i>')
                    ->nickname('shadcn_demo')
                    ->data('permission', 'admin')
                    ->link->attr(['class' => ''])
                    ->href('#shadcn_demo');

                // Add ShadCN Tables submenu
                $menu->shadcn_demo->add('<span>Modern Tables</span>', ['class' => 'sidebar-layout', 'route' => 'shadcn.orders'])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-table"></i>')
                    ->link->attr(['class' => '']);

                // Add Delivery Routes
                $menu->shadcn_demo->add('<span>Delivery Routes</span>', ['class' => 'sidebar-layout', 'route' => 'shadcn.orders.new'])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-route"></i>')
                    ->link->attr(['class' => '']);

                // Add ShadCN Test page
                $menu->shadcn_demo->add('<span>Test Page</span>', ['class' => 'sidebar-layout', 'route' => 'shadcn.test'])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-vial"></i>')
                    ->link->attr(['class' => '']);

                // Add API Documentation link
                $menu->add('<span>API Documentation</span>', ['class' => 'sidebar-layout', 'url' => url('/api-docs')])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-book"></i>')
                    ->link->attr(['class' => '', 'target' => '_blank']);

                // Add ShadCN Dashboard
                $menu->shadcn_demo->add('<span>Dashboard</span>', ['class' => 'sidebar-layout', 'route' => 'shadcn.dashboard'])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-tachometer-alt"></i>')
                    ->link->attr(['class' => '']);

                // Add ShadCN Dashboard with Enhanced Filters
                $menu->shadcn_demo->add('<span>Dashboard with Filters</span>', ['class' => 'sidebar-layout', 'route' => 'shadcn.dashboard.filters'])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-filter"></i>')
                    ->link->attr(['class' => '']);

                // Add ShadCN Table Example
                $menu->shadcn_demo->add('<span>Table Example</span>', ['class' => 'sidebar-layout', 'route' => 'shadcn.table.example'])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-table"></i>')
                    ->link->attr(['class' => '']);

                // Add ShadCN Orders Table
                $menu->shadcn_demo->add('<span>Orders Table</span>', ['class' => 'sidebar-layout', 'route' => 'shadcn.orders.table'])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-shipping-fast"></i>')
                    ->link->attr(['class' => '']);

                // Add ShadCN Users Table
                $menu->shadcn_demo->add('<span>Users Table</span>', ['class' => 'sidebar-layout', 'route' => 'shadcn.users.table'])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-users"></i>')
                    ->link->attr(['class' => '']);

                // Add Modern Orders
                $menu->shadcn_demo->add('<span>Modern Orders</span>', ['class' => 'sidebar-layout', 'route' => 'admin.orders-modern.index'])
                    ->data('permission', 'order-list')
                    ->prepend('<i class="fas fa-shopping-cart"></i>')
                    ->link->attr(['class' => '']);
            });
        }

        return $next($request);
    }
}
