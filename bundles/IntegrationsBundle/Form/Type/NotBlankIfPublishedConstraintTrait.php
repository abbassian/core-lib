<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Form\Type;

use Autoborna\PluginBundle\Entity\Integration;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

trait NotBlankIfPublishedConstraintTrait
{
    /**
     * Get not blank restraint if published.
     *
     * @return callback
     */
    private function getNotBlankConstraint()
    {
        return new Callback(
            function ($validateMe, ExecutionContextInterface $context): void {
                /** @var Integration $data */
                $data = $context->getRoot()->getData();
                if (!empty($data->getIsPublished()) && empty($validateMe)) {
                    $context->buildViolation('autoborna.core.value.required')->addViolation();
                }
            }
        );
    }
}
