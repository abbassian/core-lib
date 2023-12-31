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

<?php if (!empty($showMore)): ?>
<a href="<?php echo $view['router']->url('autoborna_form_index', ['search' => $searchString]); ?>" data-toggle="ajax">
    <span><?php echo $view['translator']->trans('autoborna.core.search.more', ['%count%' => $remaining]); ?></span>
</a>
<?php else: ?>
    <a href="<?php echo $view['router']->url('autoborna_form_action', ['objectAction' => 'view', 'objectId' => $form->getId()]); ?>" data-toggle="ajax">
    <?php echo $form->getName(); ?>
    <span class="label label-default pull-right" data-toggle="tooltip" title="<?php echo $view['translator']->trans('autoborna.form.form.resultcount'); ?>" data-placement="left">
        <?php echo $form->getResultCount(); ?>
    </span>
</a>
<?php endif; ?>