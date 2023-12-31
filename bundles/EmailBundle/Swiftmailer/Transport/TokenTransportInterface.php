<?php

namespace Autoborna\EmailBundle\Swiftmailer\Transport;

/**
 * Tokenized Transport Support.
 *
 * Interface InterfaceTokenTransport
 */
interface TokenTransportInterface
{
    /**
     * Function required to check that $this->message is instanceof AutobornaMessage, return $this->message->getMetadata() if it is and array() if not.
     *
     * @return array
     */
    public function getMetadata();

    /**
     * Return the max number of to addresses allowed per batch.  If there is no limit, return 0.
     *
     * @return int
     */
    public function getMaxBatchLimit();

    /**
     * Get the count for the max number of recipients per batch.
     *
     * @param int    $toBeAdded Number of emails about to be added
     * @param string $type      Type of emails being added (to, cc, bcc)
     *
     * @return int
     */
    public function getBatchRecipientCount(\Swift_Message $message, $toBeAdded = 1, $type = 'to');
}
