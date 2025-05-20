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

                // Add ShadCN Test page
                $menu->shadcn_demo->add('<span>Test Page</span>', ['class' => 'sidebar-layout', 'route' => 'shadcn.test'])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-vial"></i>')
                    ->link->attr(['class' => '']);

                // Add New Orders ShadCN
                $menu->shadcn_demo->add('<span>New Orders UI</span>', ['class' => 'sidebar-layout', 'route' => 'shadcn.new-orders'])
                    ->data('permission', 'admin')
                    ->prepend('<i class="fas fa-shopping-cart"></i>')
                    ->link->attr(['class' => '']);
            });
        }

        return $next($request);
    }
}
