<?php

namespace Autoborna\CoreBundle\Helper\ListParser;

use Autoborna\CoreBundle\Helper\ListParser\Exception\FormatNotSupportedException;

interface ListParserInterface
{
    /**
     * @param mixed $list
     *
     * @throws FormatNotSupportedException
     */
    public function parse($list): array;
}
