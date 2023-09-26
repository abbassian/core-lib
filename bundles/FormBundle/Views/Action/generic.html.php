<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>

<div class="autobornaform-row panel<?php if (empty($action['settings']['allowCampaignForm'])) {
    echo ' action-standalone-only';
} ?>" id="autobornaform_action_<?php echo $id; ?>">
    <?php
    if (!empty($inForm)) {
        echo $view->render('AutobornaFormBundle:Builder:actions.html.php', [
            'id'         => $id,
            'route'      => 'autoborna_formaction_action',
            'actionType' => 'action',
            'formId'     => $formId,
        ]);
    }
    ?>
    <a data-toggle="ajaxmodal" data-target="#formComponentModal" href="<?php echo $view['router']->path('autoborna_formaction_action', ['objectAction' => 'edit', 'objectId' => $id, 'formId' => $formId]); ?>"><span class="action-label"><?php echo $action['name']; ?></span></a>
    <?php if (!empty($action['description'])): ?>
    <span class="action-descr"><?php echo $action['description']; ?></span>
    <?php endif; ?>
</div>