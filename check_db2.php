<?php
$users = \Illuminate\Support\Facades\DB::table('users')->get();
echo "Users: " . count($users) . "\n";
foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, panel: " . ($user->access_panel ?? 'null') . "\n";
}
