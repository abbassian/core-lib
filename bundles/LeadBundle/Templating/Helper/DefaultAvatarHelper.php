<?php

namespace Autoborna\LeadBundle\Templating\Helper;

use Autoborna\CoreBundle\Helper\PathsHelper;
use Autoborna\CoreBundle\Templating\Helper\AssetsHelper;

class DefaultAvatarHelper
{
    /**
     * @var PathsHelper
     */
    private $pathsHelper;

    /**
     * @var AssetsHelper
     */
    private $assetsHelper;

    public function __construct(
        PathsHelper $pathsHelper,
        AssetsHelper $assetsHelper
    ) {
        $this->pathsHelper  = $pathsHelper;
        $this->assetsHelper = $assetsHelper;
    }

    public function getDefaultAvatar(bool $absolute = false): string
    {
        $img = $this->pathsHelper->getSystemPath('assets').'/images/avatar.png';

        return $this->assetsHelper->getUrl($img, null, null, $absolute);
    }
}
