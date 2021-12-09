<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Tests\Unit\DependencyInjection;

use Auxmoney\OpentracingHttplugBundle\DependencyInjection\HttplugPluginClientFactoryCompilerPass;
use Auxmoney\OpentracingHttplugBundle\Factory\DecoratedPluginClientFactory;
use Http\Client\Common\PluginClient;
use Http\Client\Common\PluginClientFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class HttplugPluginClientFactoryCompilerPassTest extends TestCase
{
    use ProphecyTrait;

    /** @var HttplugPluginClientFactoryCompilerPass */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new HttplugPluginClientFactoryCompilerPass();
    }

    public function dataOfNonDesiredFactories(): array
    {
        $factoryReferenceOkButWrongServiceId = $this->prophesize(Reference::class);
        $factoryReferenceOkButWrongServiceId->__toString()->willReturn('wrongServiceId'); // here!

        return [
            'null' => [null],
            'string' => ['string'],
            'first element of array is not reference' => [
                [
                    new AnyObject,
                    'factoryMethod'
                ]
            ],
            'first element of array is a reference but service ID is not PluginClientFactory' => [
                [
                    $factoryReferenceOkButWrongServiceId->reveal(),
                    'factoryMethod'
                ]
            ]
        ];
    }

    /**
     * @param string|array|null $factory
     * @dataProvider dataOfNonDesiredFactories
     */
    public function testProcessDoesNothingWhenFactoryOfPluginClientDoesNotMatch($factory): void
    {
        $clientDefinition = $this->prophesize(Definition::class);
        $clientDefinition->getClass()->willReturn(PluginClient::class);

        $clientDefinition->getFactory()->willReturn($factory);
        $clientDefinition->setFactory(Argument::any())->shouldNotBeCalled();

        $container = new ContainerBuilder();
        $container->addDefinitions([
            'client' => $clientDefinition->reveal()
        ]);

        $this->subject->process($container);
    }

    public function testProcessDoesNothingWhenFactoryOfPluginClientIsNotPluginClientFactory(): void
    {
        $clientDefinition = $this->prophesize(Definition::class);
        $clientDefinition->getClass()->willReturn(PluginClient::class);

        $clientFactory = $this->prophesize(Reference::class);
        $clientFactory->__toString()->willReturn(Reference::class); // here!

        $clientDefinition->getFactory()->willReturn([
            $clientFactory->reveal(),
            'factoryMethodOfPluginClientFactory'
        ]);

        $clientDefinition->setFactory(Argument::any())->shouldNotBeCalled();

        $container = new ContainerBuilder();
        $container->addDefinitions([
            'client' => $clientDefinition->reveal()
        ]);

        $this->subject->process($container);
    }

    public function testProcessDecoratesPluginClientFactory(): void
    {
        $clientDefinition = $this->prophesize(Definition::class);
        $clientDefinition->getClass()->willReturn(PluginClient::class);

        $clientFactory = $this->prophesize(Reference::class);
        $clientFactory->__toString()->willReturn(PluginClientFactory::class);

        $clientDefinition->getFactory()->willReturn([
            $clientFactory->reveal(),
            'factoryMethodOfPluginClientFactory'
        ]);

        $clientDefinition->setFactory(Argument::exact([
            new Reference(DecoratedPluginClientFactory::class),
            'createClient'
        ]))->shouldBeCalled();

        $container = new ContainerBuilder();
        $container->addDefinitions([
            'client' => $clientDefinition->reveal()
        ]);

        $this->subject->process($container);
    }
}
