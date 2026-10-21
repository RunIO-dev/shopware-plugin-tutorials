<?php declare(strict_types=1);

namespace Runio\ProductFaq\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
class Migration1792571341AllowQuestionsWithoutAnswer extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1792571341;
    }

    public function update(Connection $connection): void
    {
        // Questions asked by customers have no answer yet
        $connection->executeStatement('ALTER TABLE `runio_product_faq` MODIFY `answer` LONGTEXT NULL');
    }
}
