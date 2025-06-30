<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\Struct;

trait AssignArrayTrait
{
    /**
     * @param array<array-key, mixed> $options
     *
     * @return $this
     */
    public function assign(array $options)
    {
        foreach ($options as $key => $value) {
            if ($key === 'id' && method_exists($this, 'setId')) {
                $this->setId($value);

                continue;
            }

            try {
                $this->$key = $value;
            } catch (\Error|\Exception $error) {
                // nth
            }
        }

        return $this;
    }
}
