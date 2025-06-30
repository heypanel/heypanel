<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Channel\Context;

use HeyPanel\Core\Framework\Util\Random;
use HeyPanel\Core\Profiling\Profiler;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Event\ChannelContextCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChannelContextService implements ChannelContextServiceInterface
{
    final public const CURRENCY_ID = 'currencyId';
    final public const LANGUAGE_ID = 'languageId';
    final public const MEMBER_ID = 'customerId';
    final public const MEMBER_GROUP_ID = 'customerGroupId';
    final public const COUNTRY_ID = 'countryId';
    final public const VERSION_ID = 'version-id';
    final public const DOMAIN_ID = 'domainId';
    final public const ORIGINAL_CONTEXT = 'originalContext';

    public function __construct(
        private readonly AbstractChannelContextFactory $factory,
        private readonly ChannelContextPersister $contextPersister,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function get(ChannelContextServiceParameters $parameters): ChannelContext
    {
        return Profiler::trace('channel-context', function () use ($parameters) {
            $token = $parameters->getToken();
            $session = $this->contextPersister->load($token, $parameters->getChannelId());
            if ($session['expired'] ?? false) {
                $token = Random::getAlphanumericString(32);
            }

            if ($parameters->getLanguageId() !== null) {
                $session[self::LANGUAGE_ID] = $parameters->getLanguageId();
            }

            if ($parameters->getCurrencyId() !== null && !\array_key_exists(self::CURRENCY_ID, $session)) {
                $session[self::CURRENCY_ID] = $parameters->getCurrencyId();
            }

            if ($parameters->getDomainId() !== null) {
                $session[self::DOMAIN_ID] = $parameters->getDomainId();
            }

            if ($parameters->getOriginalContext() !== null) {
                $session[self::ORIGINAL_CONTEXT] = $parameters->getOriginalContext();
            }
            if ($parameters->getCustomerId() !== null) {
                $session[self::MEMBER_ID] = $parameters->getCustomerId();
            }
            $context = $this->factory->create($token, $parameters->getChannelId(), $session);
            $this->eventDispatcher->dispatch(new ChannelContextCreatedEvent($context, $token, $session));

            return $context;
        });
    }
}
