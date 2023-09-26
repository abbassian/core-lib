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
    <a href="<?php echo $view['router']->url('autoborna_campaign_index', ['search' => $searchString]); ?>" data-toggle="ajax">
        <span><?php echo $view['translator']->trans('autoborna.core.search.more', ['%count%' => $remaining]); ?></span>
    </a>
</div>
<?php else: ?>
<a href="<?php echo $view['router']->url('autoborna_campaign_action', ['objectAction' => 'view', 'objectId' => $campaign->getId()]); ?>" data-toggle="ajax">
    <?php echo $campaign->getName(); ?>
</a>
<?php endif; ?>