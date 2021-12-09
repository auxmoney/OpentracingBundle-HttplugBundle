<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Tests\Unit\Plugin;

use Auxmoney\OpentracingBundle\Internal\Decorator\RequestSpanning;
use Auxmoney\OpentracingBundle\Service\Tracing;
use Auxmoney\OpentracingHttplugBundle\Plugin\OpentracingPlugin;
use Http\Client\Exception\HttpException;
use Http\Client\Exception\TransferException;
use Http\Client\Promise\HttpFulfilledPromise;
use Http\Client\Promise\HttpRejectedPromise;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class OpentracingPluginTest extends TestCase
{
    use ProphecyTrait;

    /** @var RequestSpanning|ObjectProphecy */
    private $requestSpanning;

    /** @var Tracing|ObjectProphecy */
    private $tracing;

    public function setUp(): void
    {
        parent::setUp();
        $this->requestSpanning = $this->prophesize(RequestSpanning::class);
        $this->tracing = $this->prophesize(Tracing::class);
    }

    public function test_invoke_fulfilled(): void
    {
        $originalRequest = new Request('GET', '/foo-uri');
        $injectedRequest = $originalRequest;

        $this->tracing->injectTracingHeaders($originalRequest)
            ->shouldBeCalled()
            ->willReturn($injectedRequest);

        $this->requestSpanning->start('GET', '/foo-uri')->shouldBeCalled();
        $this->requestSpanning->finish(201)->shouldBeCalled();
        $this->tracing->setTagOfActiveSpan('auxmoney-opentracing-bundle.span-origin', 'httplug:request')->shouldBeCalled();
        $this->tracing->finishActiveSpan()->shouldBeCalled();

        $subject = new OpentracingPlugin($this->requestSpanning->reveal(), $this->tracing->reveal());

        $next = function (RequestInterface $request) use ($injectedRequest) {
            self::assertSame($request, $injectedRequest);
            return new HttpFulfilledPromise(new Response(201, [], 'some body'));
        };

        $promise = $subject->handleRequest($originalRequest, $next, static function () {
        });

        /** @var ResponseInterface $response */
        $response = $promise->wait();

        self::assertSame('some body', (string)$response->getBody());
        self::assertSame(201, $response->getStatusCode());
    }

    public function test_when_promise_is_rejected_by_an_exception_its_logged_in_active_span(): void
    {
        $originalRequest = new Request('GET', '/foo-uri');
        $injectedRequest = $originalRequest;
        $exceptionToBeThrown = new TransferException('Server problem');

        $this->tracing->injectTracingHeaders($originalRequest)
            ->shouldBeCalled()
            ->willReturn($injectedRequest);

        $this->requestSpanning->start('GET', '/foo-uri')->shouldBeCalled();
        $this->tracing->setTagOfActiveSpan('auxmoney-opentracing-bundle.span-origin', 'httplug:request')->shouldBeCalled();
        $this->tracing->logInActiveSpan([
            'event' => 'error',
            'error.kind' => 'Exception',
            'error.object' => TransferException::class,
            'message' => $exceptionToBeThrown->getMessage(),
            'stack' => $exceptionToBeThrown->getTraceAsString(),
        ])->shouldBeCalled();
        $this->tracing->finishActiveSpan()->shouldBeCalled();

        $subject = new OpentracingPlugin($this->requestSpanning->reveal(), $this->tracing->reveal());

        $next = function (RequestInterface $request) use ($injectedRequest, $exceptionToBeThrown) {
            self::assertSame($request, $injectedRequest);
            return new HttpRejectedPromise($exceptionToBeThrown);
        };

        $this->expectException(TransferException::class);
        $promise = $subject->handleRequest($originalRequest, $next, static function () {});

        /** @var ResponseInterface $response */
        $promise->wait();
    }

    public function test_when_promise_is_rejected_by_exception_that_contains_response_requestSpanning_is_finished_with_the_status_code_of_the_response(): void
    {
        $originalRequest = new Request('GET', '/foo-uri');
        $injectedRequest = $originalRequest;
        $response = new Response(504, [], 'gateway timeout');
        $exceptionToBeThrown = new HttpException('Response problem', $originalRequest, $response);

        $this->tracing->injectTracingHeaders($originalRequest)
            ->shouldBeCalled()
            ->willReturn($injectedRequest);

        $this->requestSpanning->start('GET', '/foo-uri')->shouldBeCalled();
        $this->tracing->setTagOfActiveSpan('auxmoney-opentracing-bundle.span-origin', 'httplug:request')->shouldBeCalled();
        $this->tracing->logInActiveSpan([
            'event' => 'error',
            'error.kind' => 'Exception',
            'error.object' => get_class($exceptionToBeThrown),
            'message' => $exceptionToBeThrown->getMessage(),
            'stack' => $exceptionToBeThrown->getTraceAsString(),
        ])->shouldBeCalled();
        $this->requestSpanning->finish(504)->shouldBeCalled();
        $this->tracing->finishActiveSpan()->shouldBeCalled();

        $subject = new OpentracingPlugin($this->requestSpanning->reveal(), $this->tracing->reveal());

        $next = function (RequestInterface $request) use ($injectedRequest, $exceptionToBeThrown) {
            self::assertSame($request, $injectedRequest);
            return new HttpRejectedPromise($exceptionToBeThrown);
        };

        $this->expectException(TransferException::class);
        $promise = $subject->handleRequest($originalRequest, $next, static function () {
        });

        /** @var ResponseInterface $response */
        $promise->wait();
    }
}
