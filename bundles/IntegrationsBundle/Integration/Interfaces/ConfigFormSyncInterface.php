<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Integration\Interfaces;

use Autoborna\IntegrationsBundle\Mapping\MappedFieldInfoInterface;

interface ConfigFormSyncInterface extends IntegrationInterface
{
    /**
     * Return an array of Integration objects in the format of [$object => $translatableObjectNameString].
     * i.e. ['Customer' => 'autoborna.something.object.customer', 'Account' => 'autoborna.something.object.account'];.
     */
    public function getSyncConfigObjects(): array;

    /**
     * Return an array of Integration objects and what Autoborna objects they are mapped to.
     * i.e. ['Customer' => Contact::NAME, 'Account' =>  Company::NAME];.
     */
    public function getSyncMappedObjects(): array;

    /**
     * Return an array of required fields.
     *
     * @return MappedFieldInfoInterface[]
     */
    public function getRequiredFieldsForMapping(string $object): array;

    /**
     * Return an array of optional fields.
     *
     * @return MappedFieldInfoInterface[]
     */
    public function getOptionalFieldsForMapping(string $object): array;

    /**
     * Return an array of all fields.
     *
     * @return MappedFieldInfoInterface[]
     */
    public function getAllFieldsForMapping(string $object): array;

    /**
     * Return a custom form field name to be included in the features array specific to sync.
     */
    public function getSyncConfigFormName(): ?string;
}
