<?php
declare(strict_types=1);

namespace App\Http;

use App\Http\Middleware\MiddlewareInterface;
use UnexpectedValueException;

class Dispatcher
{
    /**
     * @var MiddlewareInterface[] $stack middleware stack
     */
    protected array $stack = [];

    /**
     * @param MiddlewareInterface[] $stack middleware stack (with at least one middleware component)
     */
    public function __construct(array $stack)
    {
        $this->stack = $stack;
    }

    /**
     * Dispatches the middleware stack and returns the resulting Response.
     * @param Request $request
     * @return Response
     */
    public function dispatch(Request $request): Response
    {
        $resolved = $this->resolve(0);
        return $resolved->handle($request);
    }

    /**
     * @param int $index middleware stack index
     *
     * @return RequestHandler
     */
    private function resolve(int $index): RequestHandler
    {
        return new RequestHandler(function (Request $request) use ($index) {
            $middleware = $this->stack[$index];
            if (!($middleware instanceof MiddlewareInterface)) {
                throw new UnexpectedValueException(
                    sprintf('The middleware must be an instance of %s', MiddlewareInterface::class)
                );
            }
            return $middleware->process($request, $this->resolve($index + 1));
        });
    }
}
