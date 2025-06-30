<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Mail\Service;

use HeyPanel\Core\Content\Mail\MailException;
use Symfony\Component\Mime\Email;

abstract class AbstractMailSender
{
    abstract public function getDecorated(): AbstractMailSender;

    /**
     * @throws MailException
     */
    abstract public function send(Email $email): void;
}
