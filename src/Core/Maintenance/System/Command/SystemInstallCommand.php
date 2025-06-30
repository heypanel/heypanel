<?php declare(strict_types=1);

namespace HeyPanel\Core\Maintenance\System\Command;

use HeyPanel\Core\DevOps\Environment\EnvironmentHelper;
use HeyPanel\Core\Framework\Adapter\Console\HeyPanelStyle;
use HeyPanel\Core\Maintenance\MaintenanceException;
use HeyPanel\Core\Maintenance\System\Service\DatabaseConnectionFactory;
use HeyPanel\Core\Maintenance\System\Service\SetupDatabaseAdapter;
use HeyPanel\Core\Maintenance\System\Struct\DatabaseConnectionInformation;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal should be used over the CLI only
 */
#[AsCommand(
    name: 'system:install',
    description: 'Installs the HeyPanel 6 system',
)]
class SystemInstallCommand extends Command
{
    public function __construct(
        private readonly string $projectDir,
        private readonly SetupDatabaseAdapter $setupDatabaseAdapter,
        private readonly DatabaseConnectionFactory $databaseConnectionFactory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('create-database', null, InputOption::VALUE_NONE, 'Create database if it doesn\'t exist.')
            ->addOption('drop-database', null, InputOption::VALUE_NONE, 'Drop existing database')
            ->addOption('basic-setup', null, InputOption::VALUE_NONE, 'Create storefront channel and admin user')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force install even if install.lock exists')
            ->addOption('no-assign-theme', null, InputOption::VALUE_NONE, 'Do not assign the default theme')
            ->addOption('website-name', null, InputOption::VALUE_REQUIRED, 'The name of your shop')
            ->addOption('website-email', null, InputOption::VALUE_REQUIRED, 'Shop email address')
            ->addOption('website-locale', null, InputOption::VALUE_REQUIRED, 'Default language locale of the shop')
            ->addOption('website-currency', null, InputOption::VALUE_REQUIRED, 'Iso code for the default currency of the shop')
            ->addOption('skip-assets-install', null, InputOption::VALUE_NONE, 'Skips installing of assets')
            ->addOption('skip-first-run-wizard', null, InputOption::VALUE_NONE, 'Skips the first run wizard')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = new HeyPanelStyle($input, $output);

        // set default
        $isBlueGreen = EnvironmentHelper::getVariable('BLUE_GREEN_DEPLOYMENT', '1');
        $_SERVER['BLUE_GREEN_DEPLOYMENT'] = $isBlueGreen;
        $_ENV['BLUE_GREEN_DEPLOYMENT'] = $isBlueGreen;
        putenv('BLUE_GREEN_DEPLOYMENT=' . $isBlueGreen);

        if (!$input->getOption('force') && \is_file($this->projectDir . '/install.lock')) {
            $output->comment('install.lock already exists. Delete it or pass --force to do it anyway.');

            return self::FAILURE;
        }

        $this->initializeDatabase($output, $input);

        $commands = [
            [
                'command' => 'database:migrate',
                'identifier' => 'core',
                '--all' => true,
            ],
            [
                'command' => 'database:migrate-destructive',
                'identifier' => 'core',
                '--all' => true,
                '--version-selection-mode' => 'all',
            ],
            [
                'command' => 'system:configure-website',
                '--website-name' => $input->getOption('website-name'),
                '--website-email' => $input->getOption('website-email'),
                '--website-locale' => $input->getOption('website-locale'),
                '--website-currency' => $input->getOption('website-currency'),
                '--no-interaction' => true,
            ],
            [
                'command' => 'dal:refresh:index',
            ],
            [
                'command' => 'scheduled-task:register',
            ],
            [
                'command' => 'plugin:refresh',
            ],
        ];

        $application = $this->getConsoleApplication();
        if ($application->has('theme:refresh')) {
            $commands[] = [
                'command' => 'theme:refresh',
            ];
        }

        if ($application->has('theme:compile')) {
            $commands[] = [
                'command' => 'theme:compile',
                '--sync' => true,
                'allowedToFail' => true,
            ];
        }

        if ($input->getOption('basic-setup')) {
            $commands[] = [
                'command' => 'user:create',
                'username' => 'admin',
                '--admin' => true,
                '--password' => 'heypanel',
            ];

            if ($application->has('channel:create:frontend')) {
                $commands[] = [
                    'command' => 'channel:create:frontend',
                    '--name' => $input->getOption('website-name') ?? 'Web',
                    '--url' => (string) EnvironmentHelper::getVariable('APP_URL', 'http://localhost'),
                    '--isoCode' => $input->getOption('website-locale') ?? 'zh-CN',
                ];
            }

            if ($application->has('theme:change') && !$input->getOption('no-assign-theme')) {
                $commands[] = [
                    'command' => 'theme:change',
                    'allowedToFail' => true,
                    '--all' => true,
                    '--sync' => true,
                    'theme-name' => 'Frontend',
                ];
            }
        }

        if (!$input->getOption('skip-assets-install')) {
            $commands[] = [
                'command' => 'assets:install',
            ];
        }

        $commands[] = [
            'command' => 'cache:clear',
        ];

        if ($input->getOption('skip-first-run-wizard')) {
            $commands[] = [
                'command' => 'system:config:set',
                'key' => 'core.frw.completedAt',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];
        }

        $result = $this->runCommands($commands, $output);

        if (!\is_file($this->projectDir . '/public/.htaccess')
            && \is_file($this->projectDir . '/public/.htaccess.dist')
        ) {
            copy($this->projectDir . '/public/.htaccess.dist', $this->projectDir . '/public/.htaccess');
        }

        touch($this->projectDir . '/install.lock');

        return $result;
    }

    /**
     * @param array<int, array<string, string|bool|null>> $commands
     */
    private function runCommands(array $commands, OutputInterface $output): int
    {
        foreach ($commands as $parameters) {
            // remove params with null value
            $parameters = array_filter($parameters);

            $output->writeln('');

            $allowedToFail = $parameters['allowedToFail'] ?? false;
            unset($parameters['allowedToFail']);

            try {
                $returnCode = $this->getConsoleApplication()->doRun(new ArrayInput($parameters), $output);

                if ($returnCode !== 0 && !$allowedToFail) {
                    return $returnCode;
                }
            } catch (\Throwable $e) {
                if (!$allowedToFail) {
                    throw $e;
                }
            }
        }

        return self::SUCCESS;
    }

    private function initializeDatabase(HeyPanelStyle $output, InputInterface $input): void
    {
        $databaseConnectionInformation = DatabaseConnectionInformation::fromEnv();

        $connection = $this->databaseConnectionFactory->getConnection($databaseConnectionInformation, true);

        $output->writeln('Prepare installation');
        $output->writeln('');

        $dropDatabase = $input->getOption('drop-database');
        if ($dropDatabase) {
            $this->setupDatabaseAdapter->dropDatabase($connection, $databaseConnectionInformation->getDatabaseName());
            $output->writeln('Drop database `' . $databaseConnectionInformation->getDatabaseName() . '`');
        }

        if ($input->getOption('create-database') || $dropDatabase) {
            $this->setupDatabaseAdapter->createDatabase($connection, $databaseConnectionInformation->getDatabaseName());
            $output->writeln('Created database `' . $databaseConnectionInformation->getDatabaseName() . '`');
        }

        $importedBaseSchema = $this->setupDatabaseAdapter->initializeHeyPanelDb($connection, $databaseConnectionInformation->getDatabaseName());

        if ($importedBaseSchema) {
            $output->writeln('Imported base schema.sql');
        }

        $output->writeln('');
    }

    private function getConsoleApplication(): Application
    {
        $application = $this->getApplication();
        if (!$application instanceof Application) {
            throw MaintenanceException::consoleApplicationNotFound();
        }

        return $application;
    }
}
