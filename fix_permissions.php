<?php
// Load Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Create delivery route permissions if they don't exist
$routePermissions = [
    'delivery-routes list',
    'delivery-routes create',
    'delivery-routes edit',
    'delivery-routes delete',
    'delivery-routes view',
    'delivery-routes add',
    'delivery-routes map',
];

foreach ($routePermissions as $permission) {
    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    echo "Created permission: {$permission}\n";
}

// Get all permissions
$allPermissions = Permission::all()->pluck('name')->toArray();

// Get admin role
$adminRole = Role::where('name', 'admin')->first();

// Give all permissions to admin role
if ($adminRole) {
    $adminRole->syncPermissions($allPermissions);
    echo "All permissions assigned to admin role\n";
} else {
    echo "Admin role not found\n";
}

// Get your user and make sure they have the admin role
$user = \App\Models\User::find(1); // Assuming your user ID is 1
if ($user) {
    $user->assignRole('admin');
    echo "Admin role assigned to user ID 1\n";
} else {
    echo "User ID 1 not found\n";
}

echo "Done! Please log out and log back in to refresh your permissions.\n";
