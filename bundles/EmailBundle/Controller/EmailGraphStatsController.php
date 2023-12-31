<?php

namespace Autoborna\EmailBundle\Controller;

use Autoborna\CoreBundle\Form\Type\DateRangeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EmailGraphStatsController extends Controller
{
    /**
     * Loads a specific form into the detailed panel.
     *
     * @param int    $objectId
     * @param bool   $isVariant
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function viewAction(Request $request, $objectId, $isVariant, $dateFrom = null, $dateTo = null)
    {
        /** @var \Autoborna\EmailBundle\Model\EmailModel $model */
        $model = $this->get('autoborna.email.model.email');

        /** @var \Autoborna\EmailBundle\Entity\Email $email */
        $email = $model->getEntity($objectId);

        // Init the date range filter form
        $dateRangeValues = ['date_from' => $dateFrom, 'date_to' => $dateTo];
        $action          = $this->generateUrl('autoborna_email_action', ['objectAction' => 'view', 'objectId' => $objectId]);
        $dateRangeForm   = $this->get('form.factory')->create(DateRangeType::class, $dateRangeValues, ['action' => $action]);

        if (null === $email || !$this->get('autoborna.security')->hasEntityAccess(
                'email:emails:viewown',
                'email:emails:viewother',
                $email->getCreatedBy()
        )) {
            throw new AccessDeniedHttpException();
        }

        //get A/B test information
        list($parent, $children) = $email->getVariants();

        //get related translations
        list($translationParent, $translationChildren) = $email->getTranslations();

        // Prepare stats for bargraph
        if ($chartStatsSource = $request->query->get('stats', false)) {
            $includeVariants = ('all' === $chartStatsSource);
        } else {
            $includeVariants = (($email->isVariant() && $parent === $email) || ($email->isTranslation() && $translationParent === $email));
        }

        $dateFromObject = new \DateTime($dateFrom);
        $dateToObject   = new \DateTime($dateTo);

        if ('template' === $email->getEmailType()) {
            $stats = $model->getEmailGeneralStats(
                $email,
                $includeVariants,
                null,
                $dateFromObject,
                $dateToObject
            );
        } else {
            $stats = $model->getEmailListStats(
                $email,
                $includeVariants,
                $dateFromObject,
                $dateToObject
            );
        }

        $statsDevices = $model->getEmailDeviceStats(
            $email,
            $includeVariants,
            $dateFromObject,
            $dateToObject
        );

        return $this->render(
            'AutobornaEmailBundle:Email:graph.html.php',
            [
                'email'         => $email,
                'stats'         => $stats,
                'statsDevices'  => $statsDevices,
                'showAllStats'  => $includeVariants,
                'dateRangeForm' => $dateRangeForm->createView(),
                'isVariant'     => $isVariant,
            ]
        );
    }
}
