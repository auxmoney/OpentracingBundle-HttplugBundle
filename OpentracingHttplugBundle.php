<?php

declare(strict_types=1);

namespace Auxmoney\OpentracingHttplugBundle;

use Auxmoney\OpentracingHttplugBundle\DependencyInjection\HttplugPluginClientFactoryCompilerPass;
use Auxmoney\OpentracingHttplugBundle\DependencyInjection\OpentracingHttplugExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class OpentracingHttplugBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new HttplugPluginClientFactoryCompilerPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            -999
        );
    }
}
