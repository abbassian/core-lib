<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use Autoborna\CoreBundle\Templating\Helper\ButtonHelper;

$wrap = true;
$view['buttons']->reset($app->getRequest(), ButtonHelper::LOCATION_TOOLBAR_ACTIONS, ButtonHelper::TYPE_GROUP);
include 'action_button_helper.php';
?>
<div class="panel-body">
    <div class="box-layout">
        <div class="col-xs-6 col-lg-8 va-m form-inline">
            <?php if (isset($searchValue)): ?>
            <?php echo $view->render('AutobornaCoreBundle:Helper:search.html.php', [
                    'searchId'        => (empty($searchId)) ? null : $searchId,
                    'searchValue'     => $searchValue,
                    'action'          => (isset($action)) ? $action : '',
                    'searchHelp'      => (isset($searchHelp)) ? $searchHelp : '',
                    'target'          => (empty($target)) ? null : $target,
                    'tmpl'            => (empty($tmpl)) ? null : $tmpl,
                    'overlayDisabled' => (isset($overlayDisabled)) ? $overlayDisabled : null,
                ]); ?>
            <?php endif; ?>

            <?php if (!empty($filters)): ?>
            <?php echo $view->render('AutobornaCoreBundle:Helper:list_filters.html.php', [
                    'filters' => $filters,
                    'target'  => (empty($target)) ? null : $target,
                    'tmpl'    => (empty($tmpl)) ? null : $tmpl,
                ]); ?>
            <?php endif; ?>
        </div>

        <div class="col-xs-6 col-lg-4 va-m text-right">
            <?php if (!empty($buttonHelp)): ?>
                 <div class="input-group-btn">
                    <button class="btn btn-default btn-nospin" data-toggle="modal" data-target="#<?php echo $searchId; ?>-search-help">
                        <i class="fa fa-question-circle"></i>
                    </button>
                </div>
            <?php endif; ?>
            <?php echo $view['buttons']->renderButtons(); ?>
        </div>
    </div>
</div>
