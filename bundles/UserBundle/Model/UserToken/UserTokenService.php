<?php

namespace Autoborna\UserBundle\Model\UserToken;

use Autoborna\CoreBundle\Helper\RandomHelper\RandomHelperInterface;
use Autoborna\UserBundle\Entity\UserToken;
use Autoborna\UserBundle\Entity\UserTokenRepositoryInterface;

/**
 * Class UserTokenService.
 */
final class UserTokenService implements UserTokenServiceInterface
{
    /**
     * @var RandomHelperInterface
     */
    private $randomHelper;

    /**
     * @var UserTokenRepositoryInterface
     */
    private $userTokenRepository;

    /**
     * UserTokenService constructor.
     */
    public function __construct(
        RandomHelperInterface $randomHelper,
        UserTokenRepositoryInterface $userTokenRepository
    ) {
        $this->randomHelper        = $randomHelper;
        $this->userTokenRepository = $userTokenRepository;
    }

    /**
     * @param int $secretLength
     *
     * @return UserToken
     */
    public function generateSecret(UserToken $token, $secretLength = 32)
    {
        do {
            $randomSecret   = $this->randomHelper->generate($secretLength);
            $isSecretUnique = $this->userTokenRepository->isSecretUnique($randomSecret);
        } while (false === $isSecretUnique);

        return $token->setSecret($randomSecret);
    }

    /**
     * @return bool
     */
    public function verify(UserToken $token)
    {
        return $this->userTokenRepository->verify($token);
    }
}
