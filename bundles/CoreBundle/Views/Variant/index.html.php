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
$totalWeight   = 0;
$abStatsHeader = $view['translator']->trans('autoborna.core.ab_test.stats');
?>
<?php if (!empty($variants['properties'])): ?>
<?php if (null != $variants['parent']->getVariantStartDate()): ?>
<div class="box-layout mb-lg">
    <div class="col-xs-10 va-m">
        <h4>
            <?php echo $view['translator']->trans(
                'autoborna.core.variant_start_date',
                [
                    '%time%' => $view['date']->toTime(
                        $variants['parent']->getVariantStartDate()
                    ),
                    '%date%' => $view['date']->toShort(
                        $variants['parent']->getVariantStartDate()
                    ),
                    '%full%' => $view['date']->toTime(
                        $variants['parent']->getVariantStartDate()
                    ),
                ]
            ); ?>
        </h4>
    </div>
    <!-- button -->
    <div class="col-xs-2 va-m text-right">
        <a href="#" data-toggle="modal" data-target="#abStatsModal" class="btn btn-primary">
            <?php echo $abStatsHeader; ?>
        </a>
    </div>
</div>
<?php endif; ?>
<!--/ header -->

<!-- start: variants list -->
<ul class="list-group">
    <?php
    if ($variants['parent']) :
        echo $view->render('AutobornaCoreBundle:Variant:row.html.php',
            [
                'totalWeight'   => &$totalWeight,
                'variant'       => $variants['parent'],
                'variants'      => $variants,
                'abTestResults' => $abTestResults,
                'actionRoute'   => $actionRoute,
                'activeEntity'  => $activeEntity,
                'model'         => $model,
                'nameGetter'    => $nameGetter,
            ]
        );
    endif;
    if (count($variants['children'])):
        foreach ($variants['children'] as $id => $variant) :
            echo $view->render('AutobornaCoreBundle:Variant:row.html.php',
                [
                    'totalWeight'   => &$totalWeight,
                    'variant'       => $variant,
                    'variants'      => $variants,
                    'abTestResults' => $abTestResults,
                    'actionRoute'   => $actionRoute,
                    'activeEntity'  => $activeEntity,
                    'model'         => $model,
                    'nameGetter'    => $nameGetter,
                ]
            );
        endforeach;
    endif;
    ?>
</ul>
<!--/ end: variants list -->

<?php echo $view->render(
    'AutobornaCoreBundle:Helper:modal.html.php',
    [
        'id'     => 'abStatsModal',
        'header' => $abStatsHeader,
        'body'   => (isset($abTestResults['supportTemplate'])) ? $view->render(
            $abTestResults['supportTemplate'],
            ['results' => $abTestResults, 'variants' => $variants]
        ) : $view['translator']->trans('autoborna.core.ab_test.noresults'),
        'size' => 'lg',
    ]
); ?>
<?php endif; ?>