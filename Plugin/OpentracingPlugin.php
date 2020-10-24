<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Plugin;

use Auxmoney\OpentracingBundle\Internal\Constant;
use Auxmoney\OpentracingBundle\Internal\Decorator\RequestSpanning;
use Auxmoney\OpentracingBundle\Service\Tracing;
use Http\Client\Common\Plugin;
use Http\Client\Exception\TransferException;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

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

//        var_dump('checking');
//        var_dump($request);
//        var_dump('headers...', $request->getHeaders());

        $this->requestSpanning->start($request->getMethod(), $request->getUri()->__toString());
        $this->tracing->setTagOfActiveSpan(Constant::SPAN_ORIGIN, 'httplug:request');

        return $next($request)->then(function (ResponseInterface $response) {
            $this->onFulfilled($response);
            return $response;
        }, function (TransferException $exception) {
            $this->onRejected($exception);
            throw $exception;
        });
    }

    private function onFulfilled(ResponseInterface $response): void
    {
        $this->requestSpanning->finish($response->getStatusCode());
        $this->tracing->finishActiveSpan();
    }

    private function onRejected(TransferException $exception): void
    {
        $this->tracing->logInActiveSpan([
            'event' => 'error',
            'error.kind' => 'Exception',
            'error.object' => get_class($exception),
            'message' => $exception->getMessage(),
            'stack' => $exception->getTraceAsString(),
        ]);

        $this->tracing->finishActiveSpan();
    }
}
