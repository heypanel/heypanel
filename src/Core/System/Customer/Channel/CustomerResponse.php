<?php declare(strict_types=1);

namespace HeyPanel\Core\System\Customer\Channel;

use HeyPanel\Core\Framework\DataAbstractionLayer\PartialEntity;
use HeyPanel\Core\System\Channel\ClientApiResponse;
use HeyPanel\Core\System\Customer\CustomerEntity;

/**
 * @extends ClientApiResponse<PartialEntity|CustomerEntity>
 */
class CustomerResponse extends ClientApiResponse
{
    /**
     * If the criteria used to load the customer results in a partial entity,
     * the customer entity returned may be incomplete.
     * Use {@see CustomerResponse::getPartialCustomer} to check for a partial entity.
     */
    public function getCustomer(): CustomerEntity
    {
        if ($this->object instanceof PartialEntity) {
            return (new CustomerEntity())->assign($this->object->all());
        }

        return $this->object;
    }

    public function getPartialCustomer(): ?PartialEntity
    {
        return $this->object instanceof PartialEntity ? $this->object : null;
    }
}
