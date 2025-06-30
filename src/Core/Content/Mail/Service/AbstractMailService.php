<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Mail\Service;

use HeyPanel\Core\Framework\Context;
use Symfony\Component\Mime\Email;

abstract class AbstractMailService
{
    abstract public function getDecorated(): AbstractMailService;

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $templateData
     */
    abstract public function send(array $data, Context $context, array $templateData = []): ?Email;
}
