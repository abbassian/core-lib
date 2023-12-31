<?php

namespace Autoborna\LeadBundle\Controller;

use Autoborna\LeadBundle\Entity\Lead;

/**
 * Class LeadAccessTrait.
 */
trait LeadAccessTrait
{
    /**
     * Determines if the user has access to the lead the note is for.
     *
     * @param $lead
     * @param $action
     * @param bool   $isPlugin
     * @param string $intgegration
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|Lead
     */
    protected function checkLeadAccess($leadId, $action, $isPlugin = false, $integration = '')
    {
        if (!$leadId instanceof Lead) {
            //make sure the user has view access to this lead
            $leadModel = $this->getModel('lead');
            $lead      = $leadModel->getEntity((int) $leadId);
        } else {
            $lead   = $leadId;
            $leadId = $lead->getId();
        }

        if (null === $lead || !$lead->getId()) {
            if (method_exists($this, 'postActionRedirect')) {
                //set the return URL
                $page      = $this->get('session')->get($isPlugin ? 'autoborna.'.$integration.'.page' : 'autoborna.lead.page', 1);
                $returnUrl = $this->generateUrl($isPlugin ? 'autoborna_plugin_timeline_index' : 'autoborna_contact_index', ['page' => $page]);

                return $this->postActionRedirect(
                    [
                        'returnUrl'       => $returnUrl,
                        'viewParameters'  => ['page' => $page],
                        'contentTemplate' => $isPlugin ? 'AutobornaLeadBundle:Lead:pluginIndex' : 'AutobornaLeadBundle:Lead:index',
                        'passthroughVars' => [
                            'activeLink'    => $isPlugin ? '#autoborna_plugin_timeline_index' : '#autoborna_contact_index',
                            'autobornaContent' => 'leadTimeline',
                        ],
                        'flashes' => [
                            [
                                'type'    => 'error',
                                'msg'     => 'autoborna.lead.lead.error.notfound',
                                'msgVars' => ['%id%' => $leadId],
                            ],
                        ],
                    ]
                );
            } else {
                return $this->notFound('autoborna.contact.error.notfound');
            }
        } elseif (!$this->get('autoborna.security')->hasEntityAccess(
            'lead:leads:'.$action.'own',
            'lead:leads:'.$action.'other',
            $lead->getPermissionUser()
        )
        ) {
            return $this->accessDenied();
        } else {
            return $lead;
        }
    }

    /**
     * Returns leads the user has access to.
     *
     * @param $action
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function checkAllAccess($action, $limit)
    {
        /** @var LeadModel $model */
        $model = $this->getModel('lead');

        //make sure the user has view access to leads
        $repo = $model->getRepository();

        // order by lastactive, filter
        $leads = $repo->getEntities(
            [
                'filter' => [
                    'force' => [
                        [
                            'column' => 'l.date_identified',
                            'expr'   => 'isNotNull',
                        ],
                    ],
                ],
                'oderBy'         => 'r.last_active',
                'orderByDir'     => 'DESC',
                'limit'          => $limit,
                'hydration_mode' => 'HYDRATE_ARRAY',
            ]);

        if (null === $leads) {
            return $this->accessDenied();
        }

        foreach ($leads as $lead) {
            if (!$this->get('autoborna.security')->hasEntityAccess(
                'lead:leads:'.$action.'own',
                'lead:leads:'.$action.'other',
                $lead->getOwner()
            )
            ) {
                unset($lead);
            }
        }

        return $leads;
    }
}
