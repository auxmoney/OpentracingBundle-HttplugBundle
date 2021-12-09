<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Tests\Unit;

use Auxmoney\OpentracingHttplugBundle\DependencyInjection\HttplugPluginClientFactoryCompilerPass;
use Auxmoney\OpentracingHttplugBundle\OpentracingHttplugBundle;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OpentracingHttplugBundleTest extends TestCase
{
    use ProphecyTrait;

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
            new HttplugPluginClientFactoryCompilerPass(),
            'beforeOptimization',
            -999
        )->shouldBeCalled();

        $this->subject->build($containerBuilder->reveal());
    }
}
