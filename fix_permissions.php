<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Enums\AdminPermissionEnum;
use App\Enums\GuardNameEnum;

// 1. Create all permissions for 'admin' guard
$permissions = AdminPermissionEnum::values();
foreach ($permissions as $permissionName) {
    Permission::firstOrCreate([
        'name' => $permissionName,
        'guard_name' => 'admin' // or GuardNameEnum::ADMIN()->value depending on enum structure
    ]);
}
echo "Permissions created.\n";

// 2. Ensure Super Admin role exists and assign all permissions
$superAdminRole = Role::firstOrCreate([
    'name' => 'Super Admin',
    'guard_name' => 'admin'
]);
$superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());
echo "Super Admin role synced with all permissions.\n";

// 3. Find admin user and assign role
$adminUsers = User::where('access_panel', 'admin')->get();

if ($adminUsers->isEmpty()) {
    echo "No user found with access_panel = 'admin'. Creating a default admin user...\n";
    $user = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'access_panel' => 'admin',
        'email_verified_at' => now(),
    ]);
    $user->assignRole($superAdminRole);
    echo "Created user admin@example.com (password: password) and assigned Super Admin role.\n";
} else {
    foreach ($adminUsers as $user) {
        $user->assignRole($superAdminRole);
        echo "Assigned Super Admin role to user ID: {$user->id} ({$user->email})\n";
    }
}

// Clear permission cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
echo "Permission cache cleared.\n";
