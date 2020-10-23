<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Tests\Unit;

use Auxmoney\OpentracingHttplugBundle\DependencyInjection\AmqplibRabbitMqConsumerCompilerPass;
use Auxmoney\OpentracingHttplugBundle\DependencyInjection\AmqplibRabbitMqProducerCompilerPass;
use Auxmoney\OpentracingHttplugBundle\OpentracingHttplugBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OpentracingHttplugBundleTest extends TestCase
{
    /** @var OpentracingHttplugBundle */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new OpentracingHttplugBundle();
    }

    public function testBuild(): void
    {
        $containerBuilder = $this->prophesize(ContainerBuilder::class);

        $containerBuilder->addCompilerPass(
            new AmqplibRabbitMqProducerCompilerPass(),
            'beforeOptimization',
            -999
        )->shouldBeCalled();

        $containerBuilder->addCompilerPass(
            new AmqplibRabbitMqConsumerCompilerPass(),
            'beforeOptimization',
            -999
        )->shouldBeCalled();

        $this->subject->build($containerBuilder->reveal());
    }
}
