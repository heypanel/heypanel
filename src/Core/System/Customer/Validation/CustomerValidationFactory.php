<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Validation;

use HeyPanel\Core\Framework\Validation\DataValidationDefinition;
use HeyPanel\Core\Framework\Validation\DataValidationFactoryInterface;
use HeyPanel\Core\System\Channel\ChannelContext;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CustomerValidationFactory implements DataValidationFactoryInterface
{
    public function create(ChannelContext $context): DataValidationDefinition
    {
        $definition = new DataValidationDefinition('customer.create');

        $this->addConstraints($definition);

        return $definition;
    }

    public function update(ChannelContext $context): DataValidationDefinition
    {
        $definition = new DataValidationDefinition('customer.update');

        $this->addConstraints($definition);

        return $definition;
    }

    private function addConstraints(DataValidationDefinition $definition): void
    {
        $definition
            ->add('email', new NotBlank(), new Email(null, 'VIOLATION::INVALID_EMAIL_FORMAT_ERROR'))
            ->add('active', new Type('boolean'));
    }
}
