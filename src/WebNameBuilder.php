<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use function is_string;
use function parse_url;
use function sprintf;

use const PHP_URL_PATH;

class WebNameBuilder
{
    /**
     * \BEAR\Package\Provide\Router\WebRouter のパス作成手順に準じたパスのトランザクション名
     *
     * 例："example.com - /foo/bar"
     *
     * @param array<string, mixed> $server 環境変数
     *
     * @see \BEAR\Sunday\Provide\Router\WebRouter
     */
    public function buildBy(array $server): string
    {
        if (! isset($server['HTTP_HOST']) || ! isset($server['REQUEST_URI'])) {
            return 'web - unknown';
        }

        $site = is_string($server['HTTP_HOST']) ? $server['HTTP_HOST'] : 'unknown';
        $requestUri = is_string($server['REQUEST_URI']) ? $server['REQUEST_URI'] : '';
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = is_string($path) ? $path : '';

        return sprintf('%s - %s', $site, $path);
    }
}
