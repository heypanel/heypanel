<?php declare(strict_types=1);

namespace HeyPanel\Core\Maintenance\System\Command;

use HeyPanel\Core\Framework\Adapter\Cache\CacheClearer;
use HeyPanel\Core\Framework\Adapter\Console\HeyPanelStyle;
use HeyPanel\Core\Maintenance\System\Service\WebsiteConfigurator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal should be used over the CLI only
 */
#[AsCommand(
    name: 'system:configure-website',
    description: 'Configure website',
)]
class SystemConfigureWebsiteCommand extends Command
{
    public function __construct(
        private readonly WebsiteConfigurator $shopConfigurator,
        private readonly CacheClearer $cacheClearer
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('shop-name', null, InputOption::VALUE_REQUIRED, 'The name of your shop')
            ->addOption('shop-email', null, InputOption::VALUE_REQUIRED, 'Shop email address')
            ->addOption('shop-locale', null, InputOption::VALUE_REQUIRED, 'Default language locale of the shop')
            ->addOption('shop-currency', null, InputOption::VALUE_REQUIRED, 'Iso code for the default currency of the shop')
            ->addOption('no-interaction', 'n', InputOption::VALUE_NONE, 'Run command in non-interactive mode')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = new HeyPanelStyle($input, $output);

        $this->shopConfigurator->updateBasicInformation($input->getOption('shop-name'), $input->getOption('shop-email'));

        $output->writeln('Shop configured successfully');
        $output->writeln('');

        if ($input->getOption('shop-locale')) {
            if (!$input->getOption('no-interaction')
                && !$output->confirm('Changing the shops default locale after the fact can be destructive. Are you sure you want to continue', false)
            ) {
                $output->writeln('Aborting due to user input');

                return Command::SUCCESS;
            }

            $this->shopConfigurator->setDefaultLanguage($input->getOption('shop-locale'));
            $output->writeln('Successfully changed shop default language');
            $output->writeln('');
        }

        if ($input->getOption('shop-currency')) {
            if (!$input->getOption('no-interaction')
                && !$output->confirm('Changing the shops default currency after the fact can be destructive. Are you sure you want to continue', false)
            ) {
                $output->writeln('Aborting due to user input');

                return Command::SUCCESS;
            }

            $this->shopConfigurator->setDefaultCurrency($input->getOption('shop-currency'));
            $output->writeln('Successfully changed shop default currency');
            $output->writeln('');
        }

        $this->cacheClearer->clear();

        return Command::SUCCESS;
    }
}
