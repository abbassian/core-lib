<?php

namespace Autoborna\CoreBundle\Helper;

class AppVersion
{
    /**
     * @return string
     */
    public function getVersion()
    {
        return MAUTIC_VERSION;
    }
}
