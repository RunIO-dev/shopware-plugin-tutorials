<?php declare(strict_types=1);

use Runio\ProductFaq\Core\Content\ProductFaq\ProductFaqDefinition;
use Runio\ProductFaq\Core\Content\ProductFaq\SalesChannel\ProductFaqRoute;
use Runio\ProductFaq\Subscriber\ProductFaqSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set(ProductFaqDefinition::class)
        ->tag('shopware.entity.definition', ['entity' => 'runio_product_faq']);

    // Public, because Symfony calls the route as a controller
    $services->set(ProductFaqRoute::class)
        ->public()
        ->args([service('sales_channel.product.repository'), service('runio_product_faq.repository')]);

    $services->set(ProductFaqSubscriber::class)
        ->args([service(ProductFaqRoute::class)])
        ->tag('kernel.event_subscriber');
};
