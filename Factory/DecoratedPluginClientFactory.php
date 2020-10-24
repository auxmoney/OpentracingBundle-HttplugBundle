<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Factory;

use Auxmoney\OpentracingHttplugBundle\Plugin\OpentracingPlugin;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Client\Common\PluginClientFactory;
use Http\Client\HttpAsyncClient;
use Http\HttplugBundle\Collector\PluginClientFactory as ProfilingPluginClientFactory;
use Psr\Http\Client\ClientInterface;

class DecoratedPluginClientFactory
{
    private $pluginClientFactory;
    private $plugin;

    /**
     * PluginClientFactory is used in Symfony prod, ProfilingPluginClientFactory in Symfony dev.
     * @param PluginClientFactory|ProfilingPluginClientFactory $pluginClientFactory
     * @param OpentracingPlugin $plugin
     */
    public function __construct($pluginClientFactory, OpentracingPlugin $plugin)
    {
        $this->pluginClientFactory = $pluginClientFactory;
        $this->plugin = $plugin;
    }

    /**
     * @param ClientInterface|HttpAsyncClient $client
     * @param Plugin[]                        $plugins
     * @param array<string,string>            $options
     * @see PluginClientFactory::createClient
     */
    public function createClient($client, array $plugins = [], array $options = []): PluginClient
    {
        $plugins[] = $this->plugin;

//        echo 'decoratiooon...' . PHP_EOL;

        return $this->pluginClientFactory->createClient($client, $plugins, $options);
    }
}
