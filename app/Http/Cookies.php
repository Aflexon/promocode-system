<?php

declare(strict_types=1);

namespace App\Http;

class Cookies
{

    /**
     * Convert all cookies into an associate array of header values=
     */
    public static function toHeaders(array $cookies): array
    {
        $headers = [];
        foreach ($cookies as $name => $properties) {
            $headers[] = 'Set-Cookie: ' . self::toHeader($name, $properties);
        }
        return $headers;
    }

    /**
     * Convert to `Set-Cookie` header
     */
    protected static function toHeader(string $name, array $properties): string
    {
        $result = urlencode($name) . '=' . urlencode($properties['value']);

        if (isset($properties['expires'])) {
            if (is_string($properties['expires'])) {
                $timestamp = strtotime($properties['expires']);
            } else {
                $timestamp = (int) $properties['expires'];
            }
            if ($timestamp && $timestamp !== 0) {
                $result .= '; expires=' . gmdate('D, d-M-Y H:i:s e', $timestamp);
            }
        }

        if (
            isset($properties['samesite'])
            && in_array(strtolower($properties['samesite']), ['lax', 'strict', 'none'], true)
        ) {
            $result .= '; SameSite=' . $properties['samesite'];
        }

        if (isset($properties['secure']) && $properties['secure']) {
            $result .= '; secure';
        }

        if (isset($properties['httponly']) && $properties['httponly']) {
            $result .= '; HttpOnly';
        }

        return $result;
    }

    /**
     * Returns an associative array of cookie names and values
     */
    public static function parseHeader(string $header): array
    {
        $header = rtrim($header, "\r\n");
        $pieces = preg_split('@[;]\s*@', $header);
        $cookies = [];

        if (is_array($pieces)) {
            foreach ($pieces as $cookie) {
                $cookie = explode('=', $cookie, 2);

                if (count($cookie) === 2) {
                    $key = urldecode($cookie[0]);
                    $value = urldecode($cookie[1]);

                    if (!isset($cookies[$key])) {
                        $cookies[$key] = $value;
                    }
                }
            }
        }

        return $cookies;
    }
}
