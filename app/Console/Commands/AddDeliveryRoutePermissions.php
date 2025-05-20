<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddDeliveryRoutePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:add-delivery-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add delivery route permissions and assign them to super-admin and admin roles';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Create permissions for delivery routes
        $permissions = [
            'delivery-routes list',
            'delivery-routes create',
            'delivery-routes edit',
            'delivery-routes delete',
            'delivery-routes view',
            'delivery-routes add',
            'delivery-routes map',
        ];

        $this->info('Creating delivery route permissions...');
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            $this->line("Created permission: {$permission}");
        }

        // Assign permissions to super admin role
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
            $this->info('Assigned permissions to super-admin role');
        } else {
            $this->warn('Super-admin role not found');
        }

        // Assign permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
            $this->info('Assigned permissions to admin role');
        } else {
            $this->warn('Admin role not found');
        }

        $this->info('Delivery Route Permissions have been added successfully!');
        
        return 0;
    }
}
