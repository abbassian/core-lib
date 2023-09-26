<?php

namespace Autoborna\CoreBundle\Controller;

/**
 * Class FormController.
 *
 * @deprecated 2.3 - to be removed in 3.0; use AbstractFormController instead
 */
class FormController extends AbstractStandardFormController
{
    private $deprecatedModelName;
    private $deprecatedPermissionBase;
    private $deprecatedRouteBase;
    private $deprecatedSessionBase;
    private $deprecatedTranslationBase;
    private $deprecatedTemplateBase;
    private $deprecatedAutobornaContent;
    protected $activeLink;

    /**
     * @deprecated 2.3 - to be removed in 3.0; extend AbstractStandardFormController instead
     *
     * @param string $modelName       The model for this controller
     * @param string $permissionBase  Permission base for the model (i.e. form.forms or addon.yourAddon.items)
     * @param string $routeBase       Route base for the controller routes (i.e. autoborna_form or custom_addon)
     * @param string $sessionBase     Session name base for items saved to session such as filters, page, etc
     * @param string $translationBase Language string base for the shared strings
     * @param string $templateBase    Template base (i.e. YourController:Default) for the view/controller
     * @param string $activeLink      Link ID to return via ajax response
     * @param string $autobornaContent   Autoborna content string to return via ajax response for onLoad functions
     */
    protected function setStandardParameters(
        $modelName,
        $permissionBase,
        $routeBase,
        $sessionBase,
        $translationBase,
        $templateBase = null,
        $activeLink = null,
        $autobornaContent = null
    ) {
        $this->deprecatedModelName      = $modelName;
        $this->deprecatedPermissionBase = $permissionBase;
        if (0 !== strpos($sessionBase, 'autoborna.')) {
            $sessionBase = 'autoborna.'.$sessionBase;
        }
        $this->deprecatedSessionBase     = $sessionBase;
        $this->deprecatedRouteBase       = $routeBase;
        $this->deprecatedTranslationBase = $translationBase;
        $this->activeLink                = $activeLink;
        $this->deprecatedAutobornaContent   = $autobornaContent;
        $this->deprecatedTemplateBase    = $templateBase;
    }

    /**
     * @param $action
     *
     * @return array
     */
    public function getViewArguments(array $args, $action)
    {
        return $this->customizeViewArguments($args, $action);
    }

    /**
     * @param $args
     * @param $action
     *
     * @deprecated 2.6.0 to be removed in 3.0; use getViewArguments instead
     *
     * @return array
     */
    public function customizeViewArguments($args, $action)
    {
        return $args;
    }

    /**
     * @return mixed
     */
    protected function getModelName()
    {
        return $this->deprecatedModelName;
    }

    /**
     * @return mixed
     */
    protected function getJsLoadMethodPrefix()
    {
        return $this->deprecatedAutobornaContent;
    }

    /**
     * @return mixed
     */
    protected function getRouteBase()
    {
        return $this->deprecatedRouteBase;
    }

    /**
     * @param null $objectId
     *
     * @return mixed
     */
    protected function getSessionBase($objectId = null)
    {
        return null !== $this->deprecatedSessionBase ? $this->deprecatedSessionBase : parent::getSessionBase($objectId);
    }

    /**
     * @return mixed
     */
    protected function getTemplateBase()
    {
        return $this->deprecatedTemplateBase;
    }

    /**
     * @return mixed
     */
    protected function getControllerBase()
    {
        return $this->deprecatedTemplateBase;
    }

    /**
     * @return mixed
     */
    protected function getTranslationBase()
    {
        return $this->deprecatedTranslationBase;
    }

    /**
     * @return mixed
     */
    protected function getPermissionBase()
    {
        return $this->deprecatedPermissionBase;
    }
}
