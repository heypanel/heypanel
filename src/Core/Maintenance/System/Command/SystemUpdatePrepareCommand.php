<?php declare(strict_types=1);

namespace HeyPanel\Core\Maintenance\System\Command;

use HeyPanel\Core\DevOps\Environment\EnvironmentHelper;
use HeyPanel\Core\Framework\Adapter\Console\HeyPanelStyle;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Update\Event\UpdatePostPrepareEvent;
use HeyPanel\Core\Framework\Update\Event\UpdatePrePrepareEvent;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal should be used over the CLI only
 */
#[AsCommand(
    name: 'system:update:prepare',
    description: 'Prepares the update process',
)]
class SystemUpdatePrepareCommand extends Command
{
    public function __construct(private readonly ContainerInterface $container, private readonly string $heypanelVersion)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = new HeyPanelStyle($input, $output);

        $dsn = trim((string) EnvironmentHelper::getVariable('DATABASE_URL', getenv('DATABASE_URL')));
        if ($dsn === '') {
            $output->note('Environment variable \'DATABASE_URL\' not defined. Skipping ' . $this->getName() . '...');

            return self::SUCCESS;
        }

        $output->writeln('Run Update preparations');

        $context = Context::createCLIContext();

        // TODO: get new version (from composer.lock?)
        $newVersion = '';

        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(new UpdatePrePrepareEvent($context, $this->heypanelVersion, $newVersion));

        $eventDispatcher->dispatch(new UpdatePostPrepareEvent($context, $this->heypanelVersion, $newVersion));

        return self::SUCCESS;
    }
}
