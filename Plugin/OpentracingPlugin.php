<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Plugin;

use Exception;
use Http\Promise\Promise;
use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Http\Client\Exception\HttpException;
use Auxmoney\OpentracingBundle\Service\Tracing;
use Auxmoney\OpentracingBundle\Internal\Constant;
use Auxmoney\OpentracingBundle\Internal\Decorator\RequestSpanning;

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
     * @throws Exception
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $request = $this->tracing->injectTracingHeaders($request);

        $this->requestSpanning->start($request->getMethod(), $request->getUri()->__toString());
        $this->tracing->setTagOfActiveSpan(Constant::SPAN_ORIGIN, 'httplug:request');

        return $next($request)->then(function (ResponseInterface $response) {
            $this->onFulfilled($response);
            return $response;
        }, function (Exception $exception) {
            $this->onRejected($exception);
            throw $exception;
        });
    }

    private function onFulfilled(ResponseInterface $response): void
    {
        $this->requestSpanning->finish($response->getStatusCode());
        $this->tracing->finishActiveSpan();
    }

    private function onRejected(Exception $exception): void
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
