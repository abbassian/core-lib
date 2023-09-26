<?php

namespace Autoborna\CoreBundle\Controller\Api;

use Autoborna\ApiBundle\Controller\CommonApiController;
use Autoborna\CoreBundle\Helper\InputHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Class ThemeApiController.
 */
class ThemeApiController extends CommonApiController
{
    /**
     * @var Autoborna\CoreBundle\Helper\ThemeHelper
     */
    protected $themeHelper;

    public function initialize(FilterControllerEvent $event)
    {
        $this->themeHelper = $this->container->get('autoborna.helper.theme');

        parent::initialize($event);
    }

    /**
     * Accepts the zip file and installs the theme from it.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newAction(Request $request)
    {
        if (!$this->security->isGranted('core:themes:create')) {
            return $this->accessDenied();
        }

        $response  = ['success' => false];
        $themeZip  = $request->files->get('file');
        $extension = $themeZip->getClientOriginalExtension();

        if (!$themeZip) {
            return $this->returnError(
                $this->translator->trans('autoborna.core.theme.upload.empty', [], 'validators'),
                Response::HTTP_BAD_REQUEST
            );
        } elseif ('zip' !== $extension) {
            return $this->returnError(
                $this->translator->trans('autoborna.core.not.allowed.file.extension', ['%extension%' => $extension], 'validators'),
                Response::HTTP_BAD_REQUEST
            );
        } else {
            $fileName  = InputHelper::filename($themeZip->getClientOriginalName());
            $themeName = basename($fileName, '.zip');
            $dir       = $this->get('autoborna.helper.paths')->getSystemPath('themes', true);

            if (!empty($themeZip)) {
                try {
                    $themeZip->move($dir, $fileName);
                    $response['success'] = $this->themeHelper->install($dir.'/'.$fileName);
                } catch (\Exception $e) {
                    return $this->returnError(
                        $this->translator->trans($e->getMessage(), [], 'validators')
                    );
                }
            } else {
                return $this->returnError(
                    $this->translator->trans('autoborna.dashboard.upload.filenotfound', [], 'validators')
                );
            }
        }

        $view = $this->view($response);

        return $this->handleView($view);
    }

    /**
     * Get zip file of a theme.
     *
     * @param string $theme dir name
     *
     * @return BinaryFileResponse
     */
    public function getAction($theme)
    {
        if (!$this->security->isGranted('core:themes:view')) {
            return $this->accessDenied();
        }

        try {
            $themeZip = $this->themeHelper->zip($theme);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

        if (!$themeZip) {
            return $this->returnError(
                $this->translator->trans(
                    'autoborna.core.dir.not.accesssible',
                    ['%dir%' => $theme]
                )
            );
        }

        $response = new BinaryFileResponse($themeZip);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }

    /**
     * List the folders (themes) in the /themes directory.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction()
    {
        if (!$this->security->isGranted('core:themes:view')) {
            return $this->accessDenied();
        }

        try {
            $themes = $this->themeHelper->getInstalledThemes('all', true, false, false);
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

        $view = $this->view(['themes' => $themes]);

        return $this->handleView($view);
    }

    /**
     * Delete a theme.
     *
     * @param string $theme
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction($theme)
    {
        if (!$this->security->isGranted('core:themes:delete')) {
            return $this->accessDenied();
        }

        try {
            $this->themeHelper->delete($theme);
            $response = ['success' => true];
        } catch (\Exception $e) {
            return $this->returnError($e->getMessage());
        }

        $view = $this->view($response);

        return $this->handleView($view);
    }
}
