<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Channel;

use HeyPanel\Core\Framework\Plugin\Exception\DecorationPatternException;
use HeyPanel\Core\Framework\Util\Random;
use HeyPanel\Core\Framework\Validation\DataBag\RequestDataBag;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Channel\Context\ChannelContextPersister;
use HeyPanel\Core\System\Channel\ContextTokenResponse;
use HeyPanel\Core\System\Customer\CustomerEntity;
use HeyPanel\Core\System\Customer\Event\CustomerLogoutEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['client-api']])]
class LogoutRoute extends AbstractLogoutRoute
{
    /**
     * @internal
     */
    public function __construct(
        private readonly ChannelContextPersister $contextPersister,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function getDecorated(): AbstractLogoutRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/client-api/account/logout', name: 'client-api.account.logout', defaults: ['_loginRequired' => true, '_loginRequiredAllowGuest' => true], methods: ['POST'])]
    public function logout(ChannelContext $context, RequestDataBag $data): ContextTokenResponse
    {
        /** @var CustomerEntity $customer */
        $customer = $context->getCustomer();
        if ($this->shouldDelete($context)) {
            $this->contextPersister->delete($context->getToken(), $context->getChannelId());
        } else {
            $this->contextPersister->replace($context->getToken(), $context);
        }

        $context->assign([
            'token' => Random::getAlphanumericString(32),
        ]);

        $event = new CustomerLogoutEvent($context, $customer);
        $this->eventDispatcher->dispatch($event);

        return new ContextTokenResponse($context->getToken());
    }

    private function shouldDelete(ChannelContext $context): bool
    {
        if ($context->getCustomer() === null) {
            return true;
        }

        return false;
    }
}
