<?php
$users = \App\Models\User::all();
echo "Users:\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}\n";
    echo "Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "Permissions count: " . $user->permissions()->count() . "\n";
    echo "Direct Permissions: " . $user->getDirectPermissions()->pluck('name')->join(', ') . "\n";
}

$roles = \Spatie\Permission\Models\Role::all();
echo "\nRoles:\n";
foreach ($roles as $role) {
    echo "ID: {$role->id}, Name: {$role->name}, Guard: {$role->guard_name}\n";
    echo "Permissions count: " . $role->permissions()->count() . "\n";
}
