<?php
declare(strict_types=1);

namespace App\Http;

class Request {

    /**
     * Create new HTTP request from globals
     */
    public static function createFromGlobals(array $globals): Request
    {
        return new static(
            $globals['REQUEST_METHOD'] ?? 'GET',
            $globals['REQUEST_URI'] ?? '',
            $globals,
            Cookies::parseHeader(getallheaders()['Cookie'])
        );
    }

    /**
     * Create new HTTP request
     */
    public function __construct(
        protected string $method,
        protected string $uri,
        protected array $serverParams,
        protected array $cookies
    ) {
    }

    /**
     * Retrieve server parameters.
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * Retrieve a server parameter
     *
     * @param  string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getServerParam(string $key, mixed $default = null): mixed
    {
        $serverParams = $this->getServerParams();
        return $serverParams[$key] ?? $default;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }
}
