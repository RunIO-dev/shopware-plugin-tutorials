<?php declare(strict_types=1);

namespace Runio\ProductFaq\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Migration\Traits\ImportTranslationsTrait;
use Shopware\Core\Migration\Traits\Translations;

/**
 * @internal
 *
 * A mail template type for our flow trigger, and a ready-made template in English and German
 */
class Migration1792572680AddQuestionAskedMailTemplate extends MigrationStep
{
    use ImportTranslationsTrait;

    public const TYPE = 'runio_product_faq.asked';

    public function getCreationTimestamp(): int
    {
        return 1792572680;
    }

    public function update(Connection $connection): void
    {
        if ($connection->fetchOne('SELECT 1 FROM mail_template_type WHERE technical_name = ?', [self::TYPE])) {
            return;
        }

        // All or nothing, so a failed run can simply run again
        $connection->transactional(function (Connection $connection): void {
            $now = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);
            $typeId = Uuid::randomBytes();
            $templateId = Uuid::randomBytes();

            $connection->insert('mail_template_type', [
                'id' => $typeId,
                'technical_name' => self::TYPE,
                'available_entities' => json_encode(['customer' => 'customer', 'product' => 'product', 'salesChannel' => 'sales_channel']),
                'created_at' => $now,
            ]);
            $connection->insert('mail_template', [
                'id' => $templateId,
                'mail_template_type_id' => $typeId,
                'system_default' => 1,
                'created_at' => $now,
            ]);

            // German for German languages, English for all others, including the system language
            $this->importTranslation('mail_template_type_translation', new Translations(
                ['mail_template_type_id' => $typeId, 'name' => self::DE['type']],
                ['mail_template_type_id' => $typeId, 'name' => self::EN['type']],
            ), $connection);
            $this->importTranslation('mail_template_translation', new Translations(
                $this->template($templateId, self::DE),
                $this->template($templateId, self::EN),
            ), $connection);
        });
    }

    /**
     * @param array<string, string> $text
     *
     * @return array<string, string>
     */
    private function template(string $templateId, array $text): array
    {
        return [
            'mail_template_id' => $templateId,
            'sender_name' => '{{ salesChannel.translated.name }}',
            'subject' => $text['subject'],
            'description' => $text['type'],
            'content_html' => $text['html'],
            'content_plain' => $text['plain'],
        ];
    }

    private const EN = [
        'type' => 'Product question asked',
        'subject' => 'New question about {{ product.translated.name }}',
        'html' => '<p>{{ customer.firstName }} {{ customer.lastName }} ({{ customer.email }}) asked about <strong>{{ product.translated.name }}</strong>:</p>
<blockquote>{{ question }}</blockquote>
<p>Answer it in the administration under Catalogues &rarr; Product FAQ. It stays hidden until you answer and activate it.</p>',
        'plain' => '{{ customer.firstName }} {{ customer.lastName }} ({{ customer.email }}) asked about {{ product.translated.name }}:

{{ question }}

Answer it in the administration under Catalogues > Product FAQ. It stays hidden until you answer and activate it.',
    ];

    private const DE = [
        'type' => 'Produktfrage gestellt',
        'subject' => 'Neue Frage zu {{ product.translated.name }}',
        'html' => '<p>{{ customer.firstName }} {{ customer.lastName }} ({{ customer.email }}) fragt zu <strong>{{ product.translated.name }}</strong>:</p>
<blockquote>{{ question }}</blockquote>
<p>Beantworte die Frage in der Administration unter Kataloge &rarr; Produkt-FAQ. Sie bleibt verborgen, bis du sie beantwortest und aktivierst.</p>',
        'plain' => '{{ customer.firstName }} {{ customer.lastName }} ({{ customer.email }}) fragt zu {{ product.translated.name }}:

{{ question }}

Beantworte die Frage in der Administration unter Kataloge > Produkt-FAQ. Sie bleibt verborgen, bis du sie beantwortest und aktivierst.',
    ];
}
