<?php declare(strict_types=1);

use Demo\TopBar\Subscriber\TopBarSubscriber;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(TopBarSubscriber::class)
        ->args([service(SystemConfigService::class)])
        ->tag('kernel.event_subscriber');
};
