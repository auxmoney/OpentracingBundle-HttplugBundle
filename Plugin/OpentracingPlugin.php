<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Plugin;

use Http\Client\Common\Plugin;
use Http\Message\Encoding\ChunkStream;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;

/**
 * Injects tracing headers into every request.
 */
final class OpentracingPlugin implements Plugin
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $request = $request->withAddedHeader('custom-header', 'value');

        return $next($request);
    }
}
