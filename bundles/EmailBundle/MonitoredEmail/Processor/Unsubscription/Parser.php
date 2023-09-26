<?php

namespace Autoborna\EmailBundle\MonitoredEmail\Processor\Unsubscription;

use Autoborna\EmailBundle\MonitoredEmail\Exception\UnsubscriptionNotFound;
use Autoborna\EmailBundle\MonitoredEmail\Message;

class Parser
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * Parser constructor.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * @return UnsubscribedEmail
     *
     * @throws UnsubscriptionNotFound
     */
    public function parse()
    {
        $unsubscriptionEmail = null;
        foreach ($this->message->to as $to => $name) {
            if (false !== strpos($to, '+unsubscribe')) {
                $unsubscriptionEmail = $to;

                break;
            }
        }

        if (!$unsubscriptionEmail) {
            throw new UnsubscriptionNotFound();
        }

        return new UnsubscribedEmail($this->message->fromAddress, $unsubscriptionEmail);
    }
}
