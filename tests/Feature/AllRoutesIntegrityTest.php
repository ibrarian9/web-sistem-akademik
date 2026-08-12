<?php

use Illuminate\Support\Facades\Route;

test('all named routes in web.php can be generated without throwing RouteNotFoundException', function () {
    $routes = Route::getRoutes()->getRoutesByName();

    expect(count($routes))->toBeGreaterThan(0);

    foreach ($routes as $name => $route) {
        // Collect required parameters if any
        $parameterNames = $route->parameterNames();
        $parameters = [];
        foreach ($parameterNames as $param) {
            $parameters[$param] = '1';
        }

        // Generating route must not throw RouteNotFoundException
        $url = route($name, $parameters);
        expect($url)->toBeString()->not->toBeEmpty();
    }
});

test('all sidebar routes exist in route collection', function () {
    $sidebarPath = resource_path('views/components/sidebar.blade.php');
    $content = file_get_contents($sidebarPath);

    // Extract all route names referenced in sidebar
    preg_match_all("/'route'\s*=>\s*'([^']+)'/", $content, $matches);
    $sidebarRouteNames = array_unique(array_filter($matches[1] ?? []));

    $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());

    foreach ($sidebarRouteNames as $routeName) {
        expect(in_array($routeName, $registeredRoutes))
            ->toBeTrue("Sidebar references route '{$routeName}', but it is not defined in routes/web.php!");
    }
});

test('finance.tabungan route is registered and resolves correctly', function () {
    expect(Route::has('finance.tabungan'))->toBeTrue();
    expect(route('finance.tabungan'))->toContain('/finance/tabungan');
});
