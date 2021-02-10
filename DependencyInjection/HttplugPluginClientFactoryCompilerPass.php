<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\DependencyInjection;

use Auxmoney\OpentracingHttplugBundle\Factory\DecoratedPluginClientFactory;
use Http\Client\Common\PluginClient;
use Http\Client\Common\PluginClientFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HttplugPluginClientFactoryCompilerPass implements CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definition) {
            if ($definition->getClass() === PluginClient::class) {
                $callable = $definition->getFactory();

                if ($callable[0] instanceof Reference && (string)$callable[0] === PluginClientFactory::class) {
                    $definition->setFactory([new Reference(DecoratedPluginClientFactory::class), 'createClient']);
                }
            }
        }
    }
}
