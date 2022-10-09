<?php

namespace AskPHP\Http;

use AskPHP\Http\Exception\HttpException;

class Uri
{
    private string $scheme;
    private string $host;
    private string $path;
    private string $query;
    private string $fragment;

    /**
     * @throws HttpException
     */
    public function __construct(string $uri)
    {
        $parsed = parse_url(urldecode($uri));
        if (false === $parsed)
            throw new HttpException('The source URI string appears to be malformed.');
        $this->filterScheme($parsed);
        $this->filterParsed($parsed);
    }

    /**
     * @return string
     */
    public function scheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function host(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function query(): string
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function fragment(): string
    {
        return $this->fragment;
    }


    /**
     * @throws HttpException
     */
    private function filterScheme(array $parsed): void
    {
        $scheme = isset($parsed['scheme']) ? strtolower($parsed['scheme']) : '';
        if ('' === ($scheme) or in_array($scheme, $allowed = ['http', 'https']))
            $this->scheme = $scheme;
        else
            throw new HttpException(sprintf(
                'Unsupported scheme "%s", must be any empty string or in the set (%s).',
                $scheme, implode(', ', $allowed)
            ));
    }

    private function filterParsed(array $parsed): void
    {
        $this->host = isset($parsed['host']) ? strtolower($parsed['host']) : '';
        $this->path = '/' === ($path = $parsed['path'] ?? '/')[0] ? $path : '/' . $path;
        $this->query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $this->fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
    }
}
