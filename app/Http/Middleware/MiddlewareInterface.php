<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\RequestHandler;
use App\Http\Response;

interface MiddlewareInterface {
    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(Request $request, RequestHandler $handler): Response;
}
