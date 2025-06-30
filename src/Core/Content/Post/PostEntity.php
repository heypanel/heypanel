<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Post;

use HeyPanel\Core\Framework\DataAbstractionLayer\Entity;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use HeyPanel\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PostEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;
}
