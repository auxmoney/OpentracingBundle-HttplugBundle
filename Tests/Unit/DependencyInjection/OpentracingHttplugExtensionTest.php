<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Tests\Unit\DependencyInjection;

use Auxmoney\OpentracingHttplugBundle\DependencyInjection\OpentracingHttplugExtension;
use Auxmoney\OpentracingHttplugBundle\Factory\DecoratedPluginClientFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OpentracingHttplugExtensionTest extends TestCase
{
    private OpentracingHttplugExtension $subject;

    public function setUp(): void
    {
        $this->subject = new OpentracingHttplugExtension();
    }

    public function testLoad(): void
    {
        $container = new ContainerBuilder();

        $this->subject->load([], $container);

        self::assertArrayHasKey(DecoratedPluginClientFactory::class, $container->getDefinitions());
    }
}
