<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Validation;

use HeyPanel\Core\Framework\Validation\DataValidationDefinition;
use HeyPanel\Core\Framework\Validation\DataValidationFactoryInterface;
use HeyPanel\Core\System\Channel\ChannelContext;
use HeyPanel\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordValidationFactory implements DataValidationFactoryInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
    ) {
    }

    public function create(ChannelContext $context): DataValidationDefinition
    {
        $definition = new DataValidationDefinition('password.create');

        $this->addConstraints($definition, $context);

        return $definition;
    }

    public function update(ChannelContext $context): DataValidationDefinition
    {
        $definition = new DataValidationDefinition('password.update');

        $this->addConstraints($definition, $context);

        return $definition;
    }

    private function addConstraints(DataValidationDefinition $definition, ChannelContext $context): void
    {
        $minLength = $this->systemConfigService->getInt('core.loginRegistration.passwordMinLength', $context->getChannelId());
        $definition->add('password', new NotBlank(), new Length(['min' => $minLength, 'max' => PasswordHasherInterface::MAX_PASSWORD_LENGTH, 'maxMessage' => 'VIOLATION::PASSWORD_IS_TOO_LONG']));
    }
}
