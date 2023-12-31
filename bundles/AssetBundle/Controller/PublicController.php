<?php

namespace Autoborna\AssetBundle\Controller;

use Autoborna\CoreBundle\Controller\FormController as CommonFormController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class PublicController extends CommonFormController
{
    /**
     * @param string $slug
     *
     * @return Response
     */
    public function downloadAction($slug)
    {
        //find the asset
        $security = $this->get('autoborna.security');

        /** @var \Autoborna\AssetBundle\Model\AssetModel $model */
        $model = $this->getModel('asset');

        /** @var \Autoborna\AssetBundle\Entity\Asset $entity */
        $entity = $model->getEntityBySlugs($slug);

        if (!empty($entity)) {
            $published = $entity->isPublished();

            //make sure the asset is published or deny access if not
            if ((!$published) && (!$security->hasEntityAccess('asset:assets:viewown', 'asset:assets:viewother', $entity->getCreatedBy()))) {
                $model->trackDownload($entity, $this->request, 401);

                return $this->accessDenied();
            }

            //make sure URLs match up
            $url        = $model->generateUrl($entity, false);
            $requestUri = $this->request->getRequestUri();
            //remove query
            $query = $this->request->getQueryString();

            if (!empty($query)) {
                $requestUri = str_replace("?{$query}", '', $url);
            }

            //redirect if they don't match
            if ($requestUri != $url) {
                $model->trackDownload($entity, $this->request, 301);

                return $this->redirect($url, 301);
            }

            if ($entity->isRemote()) {
                $model->trackDownload($entity, $this->request, 200);

                // Redirect to remote URL
                $response = new RedirectResponse($entity->getRemotePath());
            } else {
                try {
                    //set the uploadDir
                    $entity->setUploadDir($this->get('autoborna.helper.core_parameters')->get('upload_dir'));
                    $contents = $entity->getFileContents();
                    $model->trackDownload($entity, $this->request, 200);
                } catch (\Exception $e) {
                    $model->trackDownload($entity, $this->request, 404);

                    return $this->notFound();
                }

                $response = new Response();

                if ($entity->getDisallow()) {
                    $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
                }

                $response->headers->set('Content-Type', $entity->getFileMimeType());

                $stream = $this->request->get('stream', 0);
                if (!$stream) {
                    $response->headers->set('Content-Disposition', 'attachment;filename="'.$entity->getOriginalFileName());
                }
                $response->setContent($contents);
            }

            return $response;
        }

        $model->trackDownload($entity, $this->request, 404);

        return $this->notFound();
    }
}
