<?php

namespace Autoborna\ApiBundle\Entity\oAuth2;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Model\ClientInterface;
use FOS\OAuthServerBundle\Model\RefreshToken as BaseRefreshToken;
use Autoborna\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class RefreshToken.
 */
class RefreshToken extends BaseRefreshToken
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var \Autoborna\UserBundle\Entity\User
     */
    protected $user;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var int
     */
    protected $expiresAt;

    /**
     * @var string
     */
    protected $scope;

    public static function loadMetadata(ORM\ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('oauth2_refreshtokens')
            ->addIndex(['token'], 'oauth2_refresh_token_search');

        $builder->createField('id', 'integer')
            ->isPrimaryKey()
            ->generatedValue()
            ->build();

        $builder->createManyToOne('client', 'Client')
            ->addJoinColumn('client_id', 'id', false, false, 'CASCADE')
            ->build();

        $builder->createManyToOne('user', 'Autoborna\UserBundle\Entity\User')
            ->addJoinColumn('user_id', 'id', false, false, 'CASCADE')
            ->build();

        $builder->createField('token', 'string')
            ->unique()
            ->build();

        $builder->createField('expiresAt', 'bigint')
            ->columnName('expires_at')
            ->nullable()
            ->build();

        $builder->createField('scope', 'string')
            ->nullable()
            ->build();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client.
     *
     * @return RefreshToken
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client.
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set user.
     *
     * @param UserInterface $user
     *
     * @return RefreshToken
     */
    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
