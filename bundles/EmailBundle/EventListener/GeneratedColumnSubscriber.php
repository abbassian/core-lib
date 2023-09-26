<?php

declare(strict_types=1);

namespace Autoborna\EmailBundle\EventListener;

use Autoborna\CoreBundle\CoreEvents;
use Autoborna\CoreBundle\Doctrine\GeneratedColumn\GeneratedColumn;
use Autoborna\CoreBundle\Event\GeneratedColumnsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GeneratedColumnSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::ON_GENERATED_COLUMNS_BUILD => ['onGeneratedColumnsBuild', 0],
        ];
    }

    public function onGeneratedColumnsBuild(GeneratedColumnsEvent $event): void
    {
        $sentDate = new GeneratedColumn('email_stats', 'generated_sent_date', 'DATE', "CONCAT(YEAR(date_sent), '-', LPAD(MONTH(date_sent), 2, '0'), '-', LPAD(DAY(date_sent), 2, '0'))");
        $sentDate->addIndexColumn('email_id');
        $sentDate->setOriginalDateColumn('date_sent', 'd');

        $event->addGeneratedColumn($sentDate);
    }
}
