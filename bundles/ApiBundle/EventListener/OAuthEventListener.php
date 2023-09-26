<?php

namespace Autoborna\ApiBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\OAuthServerBundle\Event\OAuthEvent;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;

class OAuthEventListener
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Autoborna\CoreBundle\Security\Permissions\CorePermissions
     */
    private $autobornaSecurity;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    private $translator;

    /**
     * OAuthEventListener constructor.
     */
    public function __construct(EntityManager $entityManager, CorePermissions $corePermissions, TranslatorInterface $translator)
    {
        $this->em             = $entityManager;
        $this->autobornaSecurity = $corePermissions;
        $this->translator     = $translator;
    }

    /**
     * @throws AccessDeniedException
     */
    public function onPreAuthorizationProcess(OAuthEvent $event)
    {
        if ($user = $this->getUser($event)) {
            //check to see if user has api access
            if (!$this->autobornaSecurity->isGranted('api:access:full')) {
                throw new AccessDeniedException($this->translator->trans('autoborna.core.error.accessdenied', [], 'flashes'));
            }
            $client = $event->getClient();
            $event->setAuthorizedClient(
                $client->isAuthorizedClient($user, $this->em)
            );
        }
    }

    public function onPostAuthorizationProcess(OAuthEvent $event)
    {
        if ($event->isAuthorizedClient()) {
            if (null !== $client = $event->getClient()) {
                $user = $this->getUser($event);
                $client->addUser($user);
                $this->em->persist($client);
                $this->em->flush();
            }
        }
    }

    /**
     * @return mixed
     */
    protected function getUser(OAuthEvent $event)
    {
        return $this->em->getRepository('AutobornaUserBundle:User')->findOneByUsername($event->getUser()->getUsername());
    }
}
