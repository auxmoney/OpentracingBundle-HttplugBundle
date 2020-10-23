<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\DependencyInjection;

use Auxmoney\OpentracingHttplugBundle\Factory\DecoratedPluginClientFactory;
use Http\Client\Common\PluginClientFactory;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HttplugPluginFactoryCompilerPass implements CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $clientServiceName => $definition) {
            if ($definition->getClass() === PluginClient::class) {

                $callable = $definition->getFactory();

                if ($callable[0] instanceof Reference && strpos((string)$callable[0], 'httplug.client') === 0) {
//                    var_dump($clientServiceName);
//                    var_dump($definition->getTags());
//                    var_dump($definition->getArguments());
//                    var_dump($definition->getFactory());
                    $definition->setFactory([new Reference(DecoratedPluginClientFactory::class), 'createClient']);
                }
            }
        }
    }
}
