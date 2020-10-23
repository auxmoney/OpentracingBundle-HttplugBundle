<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Tests\Unit\DependencyInjection;

use Auxmoney\OpentracingHttplugBundle\DependencyInjection\OpentracingHttplugExtension;
use Auxmoney\OpentracingBundle\EventListener\FinishCommandSpanSubscriber;
use Auxmoney\OpentracingBundle\EventListener\StartCommandSpanSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OpentracingHttplugExtensionTest extends TestCase
{
    /** @var OpentracingHttplugExtension */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new OpentracingHttplugExtension();
    }

    public function testLoad(): void
    {
        $container = new ContainerBuilder();

        $this->subject->load([], $container);

        self::assertTrue($container->hasDefinition(AfterMessageProcessingSubscriber::class));
        self::assertTrue($container->hasDefinition(BeforeMessageProcessingSubscriber::class));

        self::assertTrue($container->hasDefinition(StartCommandSpanSubscriberDecorator::class));
        $startCommandSpanDefinition = $container->getDefinition(StartCommandSpanSubscriberDecorator::class);
        $expectedDefinitionInnerId = sprintf('%s.inner', StartCommandSpanSubscriberDecorator::class);
        self::assertEquals($expectedDefinitionInnerId, $startCommandSpanDefinition->getArgument(0)->__toString());
        self::assertEquals(StartCommandSpanSubscriber::class, $startCommandSpanDefinition->getDecoratedService()[0]);

        self::assertTrue($container->hasDefinition(FinishCommandSpanSubscriberDecorator::class));
        $finishCommandSpanDefinition = $container->getDefinition(FinishCommandSpanSubscriberDecorator::class);
        $expectedDefinitionInnerId = sprintf('%s.inner', FinishCommandSpanSubscriberDecorator::class);
        self::assertEquals($expectedDefinitionInnerId, $finishCommandSpanDefinition->getArgument(0)->__toString());
        self::assertEquals(FinishCommandSpanSubscriber::class, $finishCommandSpanDefinition->getDecoratedService()[0]);
    }
}
