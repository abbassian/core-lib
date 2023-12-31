<?php

namespace Autoborna\UserBundle\Security\Provider;

use Autoborna\CoreBundle\Helper\EncryptionHelper;
use Autoborna\UserBundle\Entity\PermissionRepository;
use Autoborna\UserBundle\Entity\User;
use Autoborna\UserBundle\Entity\UserRepository;
use Autoborna\UserBundle\Event\UserEvent;
use Autoborna\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider.
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var PermissionRepository
     */
    protected $permissionRepository;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var UserPasswordEncoder
     */
    protected $encoder;

    public function __construct(
        UserRepository $userRepository,
        PermissionRepository $permissionRepository,
        Session $session,
        EventDispatcherInterface $dispatcher,
        UserPasswordEncoder $encoder
    ) {
        $this->userRepository       = $userRepository;
        $this->permissionRepository = $permissionRepository;
        $this->session              = $session;
        $this->dispatcher           = $dispatcher;
        $this->encoder              = $encoder;
    }

    /**
     * @param string $username
     *
     * @return User
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $q = $this->userRepository
            ->createQueryBuilder('u')
            ->select('u, r')
            ->leftJoin('u.role', 'r')
            ->where('u.username = :username OR u.email = :username')
            ->andWhere('u.isPublished = :true')
            ->setParameter('true', true, 'boolean')
            ->setParameter('username', $username);

        $user = $q->getQuery()->getOneOrNullResult();

        if (empty($user)) {
            $message = sprintf(
                'Unable to find an active admin AutobornaUserBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0);
        }

        //load permissions
        if ($user->getId()) {
            $permissions = $this->permissionRepository->getPermissionsByRole($user->getRole());
            $user->setActivePermissions($permissions);
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $this->userRepository->getClassName() === $class
        || is_subclass_of($class, $this->userRepository->getClassName());
    }

    /**
     * Create/update user from authentication plugins.
     *
     * @param bool|true $createIfNotExists
     *
     * @return User
     *
     * @throws BadCredentialsException
     */
    public function saveUser(User $user, $createIfNotExists = true)
    {
        $isNew = !$user->getId();

        if ($isNew) {
            $user = $this->findUser($user);
            if (!$user->getId() && !$createIfNotExists) {
                throw new BadCredentialsException();
            }
        }

        // Validation for User objects returned by a plugin
        if (!$user->getRole()) {
            throw new AuthenticationException('autoborna.integration.sso.error.no_role');
        }

        if (!$user->getUsername()) {
            throw new AuthenticationException('autoborna.integration.sso.error.no_username');
        }

        if (!$user->getEmail()) {
            throw new AuthenticationException('autoborna.integration.sso.error.no_email');
        }

        if (!$user->getFirstName() || !$user->getLastName()) {
            throw new AuthenticationException('autoborna.integration.sso.error.no_name');
        }

        // Check for plain password
        $plainPassword = $user->getPlainPassword();
        if ($plainPassword) {
            // Encode plain text
            $user->setPassword(
                $this->encoder->encodePassword($user, $plainPassword)
            );
        } elseif (!$password = $user->getPassword()) {
            // Generate and encode a random password
            $user->setPassword(
                $this->encoder->encodePassword($user, EncryptionHelper::generateKey())
            );
        }

        $event = new UserEvent($user, $isNew);

        if ($this->dispatcher->hasListeners(UserEvents::USER_PRE_SAVE)) {
            $event = $this->dispatcher->dispatch(UserEvents::USER_PRE_SAVE, $event);
        }

        $this->userRepository->saveEntity($user);

        if ($this->dispatcher->hasListeners(UserEvents::USER_POST_SAVE)) {
            $this->dispatcher->dispatch(UserEvents::USER_POST_SAVE, $event);
        }

        return $user;
    }

    /**
     * @return User
     */
    public function findUser(User $user)
    {
        try {
            // Try by username
            $user = $this->loadUserByUsername($user->getUsername());

            return $user;
        } catch (UsernameNotFoundException $exception) {
            // Try by email
            try {
                return $this->loadUserByUsername($user->getEmail());
            } catch (UsernameNotFoundException $exception) {
            }
        }

        return $user;
    }
}
