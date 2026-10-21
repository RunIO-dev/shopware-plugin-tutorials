<?php declare(strict_types=1);

namespace Runio\ProductFaq\Core\Content\ProductFaq\ScheduledTask;

use Psr\Log\LoggerInterface;
use Runio\ProductFaq\Core\Content\ProductFaq\ProductFaqCollection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskCollection;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: DeleteUnansweredQuestionsTask::class)]
final class DeleteUnansweredQuestionsTaskHandler extends ScheduledTaskHandler
{
    private const BATCH_SIZE = 500;

    /**
     * @param EntityRepository<ScheduledTaskCollection> $scheduledTaskRepository
     * @param EntityRepository<ProductFaqCollection> $faqRepository
     */
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        LoggerInterface $exceptionLogger,
        private readonly EntityRepository $faqRepository,
        private readonly SystemConfigService $systemConfigService,
    ) {
        parent::__construct($scheduledTaskRepository, $exceptionLogger);
    }

    public function run(): void
    {
        $days = $this->systemConfigService->getInt('RunioProductFaq.config.deleteUnansweredAfterDays');

        // 0 keeps unanswered questions forever
        if ($days <= 0) {
            return;
        }

        // createdAt is stored in UTC
        $before = $this->now()->setTimezone(new \DateTimeZone('UTC'))->modify(\sprintf('-%d days', $days));

        // Inactive, without an answer, and older than that. Drafts with an answer stay.
        $criteria = (new Criteria())
            ->addFilter(new EqualsFilter('active', false))
            ->addFilter(new OrFilter([
                new EqualsFilter('answer', null),
                new EqualsFilter('answer', ''),
            ]))
            ->addFilter(new RangeFilter('createdAt', [
                RangeFilter::LT => $before->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]))
            ->setLimit(self::BATCH_SIZE);

        $context = Context::createCLIContext();

        // In batches, so a large backlog never loads all at once. If a batch comes back again,
        // something stopped the delete, so we stop too instead of looping forever.
        $previous = [];
        while ($ids = $this->faqRepository->searchIds($criteria, $context)->getIds()) {
            if (array_intersect($ids, $previous)) {
                break;
            }

            $this->faqRepository->delete(array_map(static fn (string $id): array => ['id' => $id], $ids), $context);
            $previous = $ids;
        }
    }
}
