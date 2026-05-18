<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('access_panel', 'admin')->first();

if (!$user) {
    echo "NO_ADMIN_USER\n";
    exit(0);
}

$request = Illuminate\Http\Request::create('/admin/dashboard', 'GET');
$request->setUserResolver(fn () => $user);

$controller = $app->make(App\Http\Controllers\Admin\DashboardController::class);
$response = $controller->index($request);
$html = $response->render();

echo (str_contains($html, 'Welcome back') ? 'HAS_WELCOME' : 'NO_WELCOME') . PHP_EOL;
echo (str_contains($html, 'chart-revenue-bg') ? 'HAS_CHART' : 'NO_CHART') . PHP_EOL;
echo (str_contains($html, 'Copyright') ? 'HAS_COPYRIGHT' : 'NO_COPYRIGHT') . PHP_EOL;
echo 'USER_ACCESS_PANEL=' . ($user->access_panel->value ?? (string) $user->access_panel) . PHP_EOL;
echo 'USER_NAME=' . ($user->name ?? '') . PHP_EOL;
