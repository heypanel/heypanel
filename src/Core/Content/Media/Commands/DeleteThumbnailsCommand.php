<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Media\Commands;

use Doctrine\DBAL\Connection;
use HeyPanel\Core\Framework\Adapter\Console\HeyPanelStyle;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'media:delete-local-thumbnails',
    description: 'Deletes all physical media thumbnails when remote thumbnails is enabled.',
)]
class DeleteThumbnailsCommand extends Command
{
    /**
     * @internal
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly EntityRepository $thumbnailRepository,
        private readonly bool $remoteThumbnailsEnable = false
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new HeyPanelStyle($input, $output);

        if (!$this->remoteThumbnailsEnable) {
            $io->comment('Deleting thumbnails is only supported when remote thumbnail is enabled.');

            return self::FAILURE;
        }

        $this->deleteThumbnails();

        $io->success('Successfully deleted all thumbnails records and thumbnails files.');

        return self::SUCCESS;
    }

    private function deleteThumbnails(): void
    {
        $thumbnailIds = $this->connection->fetchAllAssociative('SELECT LOWER(HEX(`id`)) as id FROM `media_thumbnail`');

        $this->thumbnailRepository->delete($thumbnailIds, Context::createCLIContext());

        $this->connection->executeStatement('UPDATE `media` SET `thumbnails_ro` = NULL;');
    }
}
