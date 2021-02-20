<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Plugin;

use Auxmoney\OpentracingBundle\Internal\Constant;
use Auxmoney\OpentracingBundle\Internal\Decorator\RequestSpanning;
use Auxmoney\OpentracingBundle\Service\Tracing;
use Http\Client\Common\Plugin;
use Http\Client\Exception\HttpException;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class OpentracingPlugin implements Plugin
{
    private $requestSpanning;
    private $tracing;

    public function __construct(RequestSpanning $requestSpanning, Tracing $tracing)
    {
        $this->requestSpanning = $requestSpanning;
        $this->tracing = $tracing;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFunctionParametersCheck)
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $request = $this->tracing->injectTracingHeaders($request);

        $this->requestSpanning->start($request->getMethod(), $request->getUri()->__toString());
        $this->tracing->setTagOfActiveSpan(Constant::SPAN_ORIGIN, 'httplug:request');

        return $next($request)->then(function (ResponseInterface $response) {
            $this->onFulfilled($response);
            return $response;
        }, function (Throwable $exception) {
            $this->onRejected($exception);
            throw $exception;
        });
    }

    private function onFulfilled(ResponseInterface $response): void
    {
        $this->requestSpanning->finish($response->getStatusCode());
        $this->tracing->finishActiveSpan();
    }

    private function onRejected(Throwable $exception): void
    {
        $this->tracing->logInActiveSpan([
            'event' => 'error',
            'error.kind' => 'Exception',
            'error.object' => get_class($exception),
            'message' => $exception->getMessage(),
            'stack' => $exception->getTraceAsString(),
        ]);

        if ($exception instanceof HttpException) {
            $this->requestSpanning->finish($exception->getResponse()->getStatusCode());
        }

        $this->tracing->finishActiveSpan();
    }
}
