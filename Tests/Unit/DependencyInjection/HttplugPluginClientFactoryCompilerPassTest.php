<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Tests\Unit\DependencyInjection;

use Auxmoney\OpentracingHttplugBundle\DependencyInjection\HttplugPluginClientFactoryCompilerPass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class HttplugPluginClientFactoryCompilerPassTest extends TestCase
{
    /** @var HttplugPluginClientFactoryCompilerPass */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = new HttplugPluginClientFactoryCompilerPass();
    }

    public function testProcessNoClientDefinitions(): void
    {
        $noClientDefinition = $this->prophesize(Definition::class);
        $noClientDefinition->getClass()->willReturn(Definition::class);
        $container = new ContainerBuilder();
        $container->addDefinitions(['noclient' => $noClientDefinition->reveal()]);

        $noClientDefinition->setArguments(Argument::any())->shouldNotBeCalled();

        $this->subject->process($container);
    }

    public function testSomething(): void
    {
        $noClientDefinition = $this->prophesize(Definition::class);
        $noClientDefinition->getClass()->willReturn(Definition::class);
        $container = new ContainerBuilder();
        $container->addDefinitions(['noclient' => $noClientDefinition->reveal()]);

        $noClientDefinition->setArguments(Argument::any())->shouldNotBeCalled();

        $this->subject->process($container);
    }
}
