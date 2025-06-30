<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\ImportExport\DataAbstractionLayer;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use HeyPanel\Core\Content\ImportExport\Exception\DeleteDefaultProfileException;
use HeyPanel\Core\Content\ImportExport\ImportExportProfileDefinition;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\Command\DeleteCommand;
use HeyPanel\Core\Framework\DataAbstractionLayer\Write\Validation\PreWriteValidationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
class SystemDefaultValidator implements EventSubscriberInterface
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PreWriteValidationEvent::class => 'preValidate',
        ];
    }

    /**
     * @internal
     *
     * @throws DeleteDefaultProfileException
     */
    public function preValidate(PreWriteValidationEvent $event): void
    {
        $ids = [];
        $writeCommands = $event->getCommands();

        foreach ($writeCommands as $command) {
            if ($command->getEntityName() === ImportExportProfileDefinition::ENTITY_NAME
                && $command instanceof DeleteCommand
            ) {
                $ids[] = $command->getPrimaryKey()['id'];
            }
        }

        $filteredIds = $this->filterSystemDefaults($ids);
        if (!empty($filteredIds)) {
            $event->getExceptions()->add(new DeleteDefaultProfileException($filteredIds));
        }
    }

    /**
     * @param string[] $ids
     *
     * @return string[]
     */
    private function filterSystemDefaults(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $result = $this->connection->executeQuery(
            'SELECT id FROM import_export_profile WHERE id IN (:idList) AND system_default = 1',
            ['idList' => $ids],
            ['idList' => ArrayParameterType::BINARY]
        );

        return $result->fetchFirstColumn();
    }
}
