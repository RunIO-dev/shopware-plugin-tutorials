<?php declare(strict_types=1);

namespace Runio\ProductFaq;

use Doctrine\DBAL\Connection;
use Runio\ProductFaq\DependencyInjection\RateLimiterCompilerPass;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RunioProductFaq extends Plugin
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RateLimiterCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 500);
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `runio_product_faq`');

        // Flows that use our trigger, then our mail template type and its templates (translations are deleted with them)
        $connection->executeStatement('DELETE FROM flow WHERE event_name = ?', ['runio.product_faq.asked']);
        $type = 'runio_product_faq.asked';
        $connection->executeStatement('DELETE mail_template FROM mail_template JOIN mail_template_type t ON t.id = mail_template.mail_template_type_id WHERE t.technical_name = ?', [$type]);
        $connection->executeStatement('DELETE FROM mail_template_type WHERE technical_name = ?', [$type]);
    }
}
