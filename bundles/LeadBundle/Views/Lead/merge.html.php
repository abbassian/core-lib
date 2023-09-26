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

<?php if ('index' == $tmpl): ?>
<div class="lead-merge-form">
    <?php echo $view->render('AutobornaCoreBundle:Helper:search.html.php', [
        'searchId'    => (empty($searchId)) ? null : $searchId,
        'searchValue' => $searchValue,
        'action'      => $currentRoute,
        'searchHelp'  => false,
        'target'      => '.lead-merge-options',
        'tmpl'        => 'update',
    ]); ?>
    <div class="lead-merge-options mt-sm">
<?php endif; ?>

        <?php echo $view['form']->start($form); ?>

        <div class="hide">
            <?php echo $view['form']->row($form['buttons']); ?>
        </div>

        <?php echo $view['form']->end($form); ?>

<?php if ('index' == $tmpl): ?>
    </div>
</div>
<?php endif; ?>