<?php declare(strict_types=1);

namespace HeyPanel\Core\Content\Flow\Events;

use HeyPanel\Core\Content\Flow\Dispatching\StorableFlow;
use HeyPanel\Core\Content\MailTemplate\MailTemplateEntity;
use HeyPanel\Core\Framework\Context;
use HeyPanel\Core\Framework\Event\HeyPanelEvent;
use HeyPanel\Core\Framework\Validation\DataBag\DataBag;

class FlowSendMailActionEvent implements HeyPanelEvent
{
    public function __construct(
        private readonly DataBag $dataBag,
        private readonly MailTemplateEntity $mailTemplate,
        private readonly StorableFlow $flow
    ) {
    }

    public function getContext(): Context
    {
        return $this->flow->getContext();
    }

    public function getDataBag(): DataBag
    {
        return $this->dataBag;
    }

    public function getMailTemplate(): MailTemplateEntity
    {
        return $this->mailTemplate;
    }

    public function getStorableFlow(): StorableFlow
    {
        return $this->flow;
    }
}
