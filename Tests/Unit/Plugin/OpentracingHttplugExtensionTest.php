<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Tests\Unit\DependencyInjection;

use Auxmoney\OpentracingHttplugBundle\DependencyInjection\OpentracingHttplugExtension;
use Auxmoney\OpentracingHttplugBundle\Factory\DecoratedPluginClientFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Auxmoney\OpentracingHttplugBundle\Plugin\OpentracingPlugin;

class OpentracingPluginTest extends TestCase
{
    /** @var OpentracingPlugin */
    private $subject;

    public function setUp(): void
    {
        $this->subject = new OpentracingPlugin();
    }

    public function testLoad(): void
    {
        $container = new ContainerBuilder();

        $this->subject->load([], $container);

        self::assertArrayHasKey(DecoratedPluginClientFactory::class, $container->getDefinitions());
    }
}
