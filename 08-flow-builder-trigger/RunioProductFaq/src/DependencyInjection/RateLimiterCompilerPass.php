<?php declare(strict_types=1);

namespace Runio\ProductFaq\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Adds our limiter from rate_limiter.yaml to Shopware's rate limiter configuration
 */
class RateLimiterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        /** @var array<string, array<string, mixed>> $config */
        $config = $container->getParameter('shopware.api.rate_limiter');
        /** @var array<string, array<string, mixed>> $ours */
        $ours = Yaml::parseFile(__DIR__ . '/../Resources/config/rate_limiter.yaml');

        $container->setParameter('shopware.api.rate_limiter', $config + $ours);
    }
}
