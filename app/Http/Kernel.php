<?php
declare(strict_types=1);

namespace App\Http;

use App\Http\Middleware\MiddlewareInterface;
use App\Http\Middleware\RouteRunnerMiddleware;

class Kernel
{
    /**
     * @var MiddlewareInterface[] $stack middleware stack
     */
    protected array $globalMiddlewares;

    /**
     * HTTP Request from client.
     */
    protected Request $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals($_SERVER);
        $this->globalMiddlewares = [
            new RouteRunnerMiddleware(),
        ];
    }

    public function run(): void
    {
        $dispatcher = new Dispatcher($this->globalMiddlewares);
        $response = $dispatcher->dispatch($this->request);
        $this->respond($response);
    }

    public function respond(Response $response): void
    {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $header) {
            header($header);
        }
        if (!empty($response->getBody())) {
            printf($response->getBody());
        }
    }
}
