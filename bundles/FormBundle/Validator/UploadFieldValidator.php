<?php

namespace Autoborna\FormBundle\Validator;

use Autoborna\CoreBundle\Exception\FileInvalidException;
use Autoborna\CoreBundle\Validator\FileUploadValidator;
use Autoborna\FormBundle\Entity\Field;
use Autoborna\FormBundle\Exception\FileValidationException;
use Autoborna\FormBundle\Exception\NoFileGivenException;
use Autoborna\FormBundle\Form\Type\FormFieldFileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class UploadFieldValidator
{
    /**
     * @var FileUploadValidator
     */
    private $fileUploadValidator;

    public function __construct(FileUploadValidator $fileUploadValidator)
    {
        $this->fileUploadValidator = $fileUploadValidator;
    }

    /**
     * @return UploadedFile
     *
     * @throws FileValidationException
     * @throws NoFileGivenException
     */
    public function processFileValidation(Field $field, Request $request)
    {
        $files = $request->files->get('autobornaform');

        if (!$files || !array_key_exists($field->getAlias(), $files)) {
            throw new NoFileGivenException();
        }

        /** @var UploadedFile $file */
        $file = $files[$field->getAlias()];

        if (!$file instanceof UploadedFile) {
            throw new NoFileGivenException();
        }

        $properties = $field->getProperties();

        $maxUploadSize     = $properties[FormFieldFileType::PROPERTY_ALLOWED_FILE_SIZE];
        $allowedExtensions = $properties[FormFieldFileType::PROPERTY_ALLOWED_FILE_EXTENSIONS];

        try {
            $this->fileUploadValidator->validate($file->getSize(), $file->getClientOriginalExtension(), $maxUploadSize, $allowedExtensions, 'autoborna.form.submission.error.file.extension', 'autoborna.form.submission.error.file.size');

            return $file;
        } catch (FileInvalidException $e) {
            throw new FileValidationException($e->getMessage());
        }
    }
}
