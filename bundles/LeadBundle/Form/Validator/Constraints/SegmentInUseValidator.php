<?php

declare(strict_types=1);

namespace Autoborna\LeadBundle\Form\Validator\Constraints;

use Autoborna\LeadBundle\Entity\LeadList;
use Autoborna\LeadBundle\Model\ListModel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SegmentInUseValidator extends ConstraintValidator
{
    /**
     * @var ListModel
     */
    private $listModel;

    public function __construct(ListModel $listModel)
    {
        $this->listModel = $listModel;
    }

    /**
     * @param LeadList $leadList
     */
    public function validate($leadList, Constraint $constraint): void
    {
        if (!$constraint instanceof SegmentInUse) {
            throw new UnexpectedTypeException($constraint, SegmentInUse::class);
        }

        if (!$leadList->getId() || $leadList->getIsPublished()) {
            return;
        }

        $lists = $this->listModel->getSegmentsWithDependenciesOnSegment($leadList->getId(), 'name');

        if (count($lists)) {
            $this->context->buildViolation($constraint->message)
                ->setCode(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->setParameter('%segments%', implode(',', $lists))
                ->addViolation();
        }
    }
}
