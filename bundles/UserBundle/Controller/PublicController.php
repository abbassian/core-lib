<?php

namespace Autoborna\UserBundle\Controller;

use Autoborna\CoreBundle\Controller\FormController;
use Autoborna\UserBundle\Form\Type\PasswordResetConfirmType;
use Autoborna\UserBundle\Form\Type\PasswordResetType;
use Symfony\Component\Form\FormError;

class PublicController extends FormController
{
    /**
     * Generates a new password for the user and emails it to them.
     */
    public function passwordResetAction()
    {
        /** @var \Autoborna\UserBundle\Model\UserModel $model */
        $model = $this->getModel('user');

        $data   = ['identifier' => ''];
        $action = $this->generateUrl('autoborna_user_passwordreset');
        $form   = $this->get('form.factory')->create(PasswordResetType::class, $data, ['action' => $action]);

        ///Check for a submitted form and process it
        if ('POST' == $this->request->getMethod()) {
            if ($isValid = $this->isFormValid($form)) {
                //find the user
                $data = $form->getData();
                $user = $model->getRepository()->findByIdentifier($data['identifier']);

                if (null == $user) {
                    $form['identifier']->addError(new FormError($this->translator->trans('autoborna.user.user.passwordreset.nouserfound', [], 'validators')));
                } else {
                    try {
                        $model->sendResetEmail($user);
                        $this->addFlash('autoborna.user.user.notice.passwordreset');
                    } catch (\Exception $exception) {
                        $this->addFlash('autoborna.user.user.notice.passwordreset.error', [], 'error');
                    }

                    return $this->redirect($this->generateUrl('login'));
                }
            }
        }

        return $this->delegateView([
            'viewParameters' => [
                'form' => $form->createView(),
            ],
            'contentTemplate' => 'AutobornaUserBundle:Security:reset.html.php',
            'passthroughVars' => [
                'route' => $action,
            ],
        ]);
    }

    public function passwordResetConfirmAction()
    {
        /** @var \Autoborna\UserBundle\Model\UserModel $model */
        $model = $this->getModel('user');

        $data   = ['identifier' => '', 'password' => '', 'password_confirm' => ''];
        $action = $this->generateUrl('autoborna_user_passwordresetconfirm');
        $form   = $this->get('form.factory')->create(PasswordResetConfirmType::class, [], ['action' => $action]);
        $token  = $this->request->query->get('token');

        if ($token) {
            $this->request->getSession()->set('resetToken', $token);
        }

        ///Check for a submitted form and process it
        if ('POST' == $this->request->getMethod()) {
            if ($isValid = $this->isFormValid($form)) {
                //find the user
                $data = $form->getData();
                /** @var \Autoborna\UserBundle\Entity\User $user */
                $user = $model->getRepository()->findByIdentifier($data['identifier']);

                if (null == $user) {
                    $form['identifier']->addError(new FormError($this->translator->trans('autoborna.user.user.passwordreset.nouserfound', [], 'validators')));
                } else {
                    if ($this->request->getSession()->has('resetToken')) {
                        $resetToken = $this->request->getSession()->get('resetToken');
                        $encoder    = $this->get('security.password_encoder');

                        if ($model->confirmResetToken($user, $resetToken)) {
                            $encodedPassword = $model->checkNewPassword($user, $encoder, $data['plainPassword']);
                            $user->setPassword($encodedPassword);
                            $model->saveEntity($user);

                            $this->addFlash('autoborna.user.user.notice.passwordreset.success');

                            $this->request->getSession()->remove('resetToken');

                            return $this->redirect($this->generateUrl('login'));
                        }

                        return $this->delegateView([
                            'viewParameters' => [
                                'form' => $form->createView(),
                            ],
                            'contentTemplate' => 'AutobornaUserBundle:Security:resetconfirm.html.php',
                            'passthroughVars' => [
                                'route' => $action,
                            ],
                        ]);
                    } else {
                        $this->addFlash('autoborna.user.user.notice.passwordreset.missingtoken');

                        return $this->redirect($this->generateUrl('autoborna_user_passwordresetconfirm'));
                    }
                }
            }
        }

        return $this->delegateView([
            'viewParameters' => [
                'form' => $form->createView(),
            ],
            'contentTemplate' => 'AutobornaUserBundle:Security:resetconfirm.html.php',
            'passthroughVars' => [
                'route' => $action,
            ],
        ]);
    }
}
