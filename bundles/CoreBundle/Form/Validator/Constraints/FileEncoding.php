<?php

namespace Autoborna\CoreBundle\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class FileEncoding extends Constraint
{
    public $encodingFormatMessage = 'autoborna.core.invalid_file_encoding';
    public $encodingFormat        = '[UTF-8]';

    public function validatedBy()
    {
        return FileEncodingValidator::class;
    }
}
