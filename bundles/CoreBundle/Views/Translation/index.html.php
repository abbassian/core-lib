<?php

/*
 * @copyright   2016 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if (!isset($nameGetter)) {
    $nameGetter = 'getName';
}

if (count($translations['children']) || ($translations['parent'] && $translations['parent']->getId() !== $activeEntity->getId())): ?>
<!-- start: related translations list -->
<ul class="list-group">
    <?php
    if ($translations['parent']) :
        echo $view->render('AutobornaCoreBundle:Translation:row.html.php',
            [
                'translation'  => $translations['parent'],
                'translations' => $translations,
                'actionRoute'  => $actionRoute,
                'activeEntity' => $activeEntity,
                'model'        => $model,
                'nameGetter'   => $nameGetter,
            ]
        );
    endif;
    if (count($translations['children'])) :
        foreach ($translations['children'] as $translation) :
            echo $view->render('AutobornaCoreBundle:Translation:row.html.php',
                [
                    'translation'  => $translation,
                    'translations' => $translations,
                    'actionRoute'  => $actionRoute,
                    'activeEntity' => $activeEntity,
                    'model'        => $model,
                    'nameGetter'   => $nameGetter,
                ]
            );
    endforeach;
    endif;
    ?>
</ul>
<!--/ end: related translations list -->
<?php endif; ?>