<?php

declare(strict_types=1);

namespace Autoborna\CoreBundle\Tests\Unit\Security\Permissions;

use Autoborna\ApiBundle\Security\Permissions\ApiPermissions;
use Autoborna\AssetBundle\Security\Permissions\AssetPermissions;
use Autoborna\CampaignBundle\Security\Permissions\CampaignPermissions;
use Autoborna\CoreBundle\Helper\CoreParametersHelper;
use Autoborna\CoreBundle\Helper\UserHelper;
use Autoborna\CoreBundle\Security\Permissions\CorePermissions;
use AutobornaPlugin\AutobornaFocusBundle\Security\Permissions\FocusPermissions;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Translation\TranslatorInterface;

class CorePermissionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var MockObject|UserHelper
     */
    private $userHelper;

    /**
     * @var CorePermissions
     */
    private $corePermissions;

    /**
     * @var MockObject|TranslatorInterface
     */
    private $translator;

    /**
     * @var MockObject|CoreParametersHelper
     */
    private $coreParametersHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userHelper           = $this->createMock(UserHelper::class);
        $this->translator           = $this->createMock(TranslatorInterface::class);
        $this->coreParametersHelper = $this->createMock(CoreParametersHelper::class);
        $this->corePermissions      = new CorePermissions(
            $this->userHelper,
            $this->translator,
            $this->coreParametersHelper,
            [
                $this->mockBundleArray(ApiPermissions::class),
                $this->mockBundleArray(AssetPermissions::class),
                $this->mockBundleArray(CampaignPermissions::class),
            ],
            [
                $this->mockBundleArray(FocusPermissions::class),
            ]
        );
    }

    public function testSettingPermissionObject(): void
    {
        $this->coreParametersHelper->method('all')
            ->willReturn(['parameter_a' => 'value_a']);

        $assetPermissions = new AssetPermissions($this->coreParametersHelper);
        $this->corePermissions->setPermissionObject($assetPermissions);
        $permissionObjects = $this->corePermissions->getPermissionObjects();

        // Even though the AssetPermissions object was set upfront there are
        // still 4 objects available.
        // The other three were instantiated to keep BC.
        $this->assertCount(4, $permissionObjects);

        $this->assertSame($assetPermissions, $this->corePermissions->getPermissionObject('asset'));
        $this->assertSame($assetPermissions, $this->corePermissions->getPermissionObject(AssetPermissions::class));
        $this->assertSame($permissionObjects['campaign'], $this->corePermissions->getPermissionObject(CampaignPermissions::class));
    }

    private function mockBundleArray(string $permissionClass): array
    {
        return ['permissionClasses' => [$permissionClass => $permissionClass]];
    }
}
