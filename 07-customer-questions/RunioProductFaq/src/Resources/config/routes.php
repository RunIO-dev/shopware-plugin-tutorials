<?php declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('../../Core/**/*Route.php', 'attribute');
    $routes->import('../../Storefront/**/*Controller.php', 'attribute');
};
