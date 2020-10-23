<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle\Factory;

use Auxmoney\OpentracingHttplugBundle\Plugin\OpentracingPlugin;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Client\Common\PluginClientFactory;
use Http\Client\HttpAsyncClient;
use Psr\Http\Client\ClientInterface;

class DecoratedPluginClientFactory
{
    /**
     * @var PluginClientFactory
     */
    private $pluginClientFactory;

    private $plugin;

    public function __construct($pluginClientFactory, OpentracingPlugin $plugin)
    {
        var_dump(get_class($pluginClientFactory));
        $this->pluginClientFactory = $pluginClientFactory;
        $this->plugin = $plugin;
    }

    /**
     * @see \Http\Client\Common\PluginClientFactory::createClient
     */
    public function createClient($client, array $plugins = [], array $options = []): PluginClient
    {
        $plugins[] = $this->plugin;

        return $this->pluginClientFactory->createClient($client, $plugins, $options);
    }
}
