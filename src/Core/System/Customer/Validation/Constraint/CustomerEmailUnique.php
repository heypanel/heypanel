<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Validation\Constraint;

use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\Customer\CustomerException;
use Symfony\Component\Validator\Constraint;

class CustomerEmailUnique extends Constraint
{
    final public const MEMBER_EMAIL_NOT_UNIQUE = '79d30fe0-febf-421e-ac9b-1bfd5c9007f7';

    protected const ERROR_NAMES = [
        self::MEMBER_EMAIL_NOT_UNIQUE => 'MEMBER_EMAIL_NOT_UNIQUE',
    ];

    public string $message = 'The email address {{ email }} is already in use.';

    protected ChannelContext $channelContext;

    /**
     * @param array{channelContext: ChannelContext} $options
     *
     * @internal
     */
    public function __construct(array $options)
    {
        if (!($options['channelContext'] ?? null) instanceof ChannelContext) {
            throw CustomerException::missingOption('channelContext', self::class);
        }
        parent::__construct($options);
    }

    public function getChannelContext(): ChannelContext
    {
        return $this->channelContext;
    }
}
