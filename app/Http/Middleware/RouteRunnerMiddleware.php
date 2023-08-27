<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Controllers\PromocodeController;
use App\Http\Request;
use App\Http\RequestHandler;
use App\Http\Response;

class RouteRunnerMiddleware implements MiddlewareInterface
{
    protected array $routes = [
        '/' => [
            'GET' => [PromocodeController::class, 'index']
        ],
        '/promocode' => [
            'POST' => [PromocodeController::class, 'receivePromocode'],
        ],
    ];

    public function process(Request $request, RequestHandler $handler): Response
    {
        $parsedUrl = parse_url($request->getUri());
        $route = $this->routes[$parsedUrl['path']][$request->getMethod()] ?? null;
        if (!$route) {
            return new Response(404, [], 'Not found');
        }
        $controller = new $route[0];
        return $controller->{$route[1]}($request);
    }
}
