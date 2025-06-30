<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Mail;

use HeyPanel\Core\Content\Mail\Service\MailSender;
use HeyPanel\Core\Content\Mail\Transport\MailerTransportLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 */
class MailerConfigurationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('mailer.default_transport')) {
            $container->getDefinition('mailer.default_transport')->setFactory([
                new Reference(MailerTransportLoader::class),
                'fromString',
            ]);
        }

        $container->getDefinition('mailer.transports')->setFactory([
            new Reference(MailerTransportLoader::class),
            'fromStrings',
        ]);

        $mailer = $container->getDefinition(MailSender::class);
        // use the same message bus from symfony/mailer configuration.
        $originalMailer = $container->getDefinition('mailer.mailer');
        $mailer->replaceArgument(4, $originalMailer->getArgument(1));
    }
}
