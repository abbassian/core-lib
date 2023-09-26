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
<a href="<?php echo $view['router']->path('autoborna_email_index', ['search' => $searchString]); ?>" data-toggle="ajax">
    <span><?php echo $view['translator']->trans('autoborna.core.search.more', ['%count%' => $remaining]); ?></span>
</a>
<?php else: ?>
<a href="<?php echo $view['router']->path('autoborna_email_action', ['objectAction' => 'view', 'objectId' => $email->getId()]); ?>" data-toggle="ajax">
    <?php echo $email->getName(); ?>
    <span class="label label-default pull-right" data-toggle="tooltip" title="<?php echo $view['translator']->trans('autoborna.email.readcount'); ?>" data-placement="left">
        <?php echo $email->getReadCount(); ?>
    </span>
</a>
<?php endif; ?>