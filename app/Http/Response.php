<?php
declare(strict_types=1);

namespace App\Http;

class Response {
    /**
     * Status code
     */
    protected int $status = 200;

    /**
     * Headers list
     */
    protected array $headers = [];

    /**
     * Body
     */
    protected string | null $body = null;

    /**
     * Create new HTTP response.
     *
     * @param int $status The response status code.
     * @param array $headers The response headers.
     * @param string|null $body The response body.
     */
    public function __construct(int $status = 200, array $headers = [], string | null $body = null)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Gets the response status code.
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * Gets the response headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get body.
     */
    public function getBody(): string|null
    {
        return $this->body;
    }
}
