<?php

namespace Autoborna\LeadBundle\Tests\Helper;

use Autoborna\CoreBundle\Helper\PathsHelper;
use Autoborna\CoreBundle\Templating\Helper\AssetsHelper;
use Autoborna\CoreBundle\Templating\Helper\GravatarHelper;
use Autoborna\LeadBundle\Entity\Lead;
use Autoborna\LeadBundle\Templating\Helper\AvatarHelper;
use Autoborna\LeadBundle\Templating\Helper\DefaultAvatarHelper;

class AvatarHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AssetsHelper
     */
    private $assetsHelperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|PathsHelper
     */
    private $pathsHelperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|GravatarHelper
     */
    private $gravatarHelperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|DefaultAvatarHelper
     */
    private $defaultAvatarHelperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Lead
     */
    private $leadMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AvatarHelper
     */
    private $avatarHelper;

    protected function setUp(): void
    {
        $this->assetsHelperMock        = $this->createMock(AssetsHelper::class);
        $this->pathsHelperMock         = $this->createMock(PathsHelper::class);
        $this->gravatarHelperMock      = $this->createMock(GravatarHelper::class);
        $this->defaultAvatarHelperMock = $this->createMock(DefaultAvatarHelper::class);
        $this->leadMock                = $this->createMock(Lead::class);
        $this->avatarHelper            = new AvatarHelper($this->assetsHelperMock, $this->pathsHelperMock, $this->gravatarHelperMock, $this->defaultAvatarHelperMock);
    }

    /**
     * Test to get gravatar.
     */
    public function testGetAvatarWhenGravatar()
    {
        $this->leadMock->method('getPreferredProfileImage')
            ->willReturn('gravatar');
        $this->leadMock->method('getSocialCache')
            ->willReturn([]);
        $this->leadMock->method('getEmail')
            ->willReturn('autoborna@acquia.com');
        $this->gravatarHelperMock->method('getImage')
            ->with('autoborna@acquia.com')
            ->willReturn('gravatarImage');
        $avatar = $this->avatarHelper->getAvatar($this->leadMock);
        $this->assertSame('gravatarImage', $avatar, 'Gravatar image should be returned');
    }

    /**
     * Test to get default image.
     */
    public function testGetAvatarWhenDefault()
    {
        $this->leadMock->method('getPreferredProfileImage')
            ->willReturn('gravatar');
        $this->leadMock->method('getSocialCache')
            ->willReturn([]);
        $this->leadMock->method('getEmail')
            ->willReturn('');
        $this->defaultAvatarHelperMock->method('getDefaultAvatar')
            ->willReturn('defaultImage');
        $avatar = $this->avatarHelper->getAvatar($this->leadMock);
        $this->assertSame('defaultImage', $avatar, 'Default image image should be returned');
    }
}
