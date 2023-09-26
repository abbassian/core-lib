<?php

/*
 * @copyright   2016 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$isCurrent = ($translation->getId() === $activeEntity->getId());
?>
<li class="list-group-item bg-auto bg-<?php echo ($isCurrent) ? 'dark' : 'light'; ?>-xs">
    <div class="box-layout">
        <div class="col-md-1 va-m">
            <h3>
                <?php echo $view->render(
                    'AutobornaCoreBundle:Helper:publishstatus_icon.html.php',
                    [
                        'item'  => $translation,
                        'model' => $model,
                        'size'  => '',
                        'query' => 'size=',
                    ]
                ); ?>
            </h3>
        </div>
        <div class="col-md-7 va-m">
            <h5 class="fw-sb text-primary">
                <a href="<?php echo $view['router']->path($actionRoute, ['objectAction' => 'view', 'objectId' => $translation->getId()]); ?>" data-toggle="ajax">
                    <span><?php echo $translation->$nameGetter(); ?></span>
                </a>
                <?php if ($isCurrent) : ?>
                    <span class="label label-success"><?php echo $view['translator']->trans('autoborna.core.current'); ?></span>
                <?php endif; ?>
                <?php if ($translations['parent']->getId() === $translation->getId()) : ?>
                    <span class="label label-warning"><?php echo $view['translator']->trans('autoborna.core.parent'); ?></span>
                <?php endif; ?>
            </h5>
            <?php if (method_exists($translation, 'getAlias')): ?>
                <span class="text-white dark-sm"><?php echo $translation->getAlias(); ?></span>
            <?php endif; ?>
        </div>
        <div class="col-md-4 va-m text-right">
            <em class="text-white dark-sm"><?php echo $translation->getLanguage(); ?></em>
        </div>
    </div>
</li>
