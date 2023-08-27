<?php
declare(strict_types=1);

namespace App\Http;

class RequestHandler
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function handle(Request $request): Response
    {
        return call_user_func($this->callback, $request);
    }
}
