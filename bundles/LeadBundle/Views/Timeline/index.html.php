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

<!-- filter form -->
<form method="post" action="<?php echo $view['router']->path('autoborna_contacttimeline_action', ['leadId' => $lead->getId()]); ?>" class="panel" id="timeline-filters">
    <div class="form-control-icon pa-xs">
        <input type="text" class="form-control bdr-w-0" name="search" id="search" placeholder="<?php echo $view['translator']->trans('autoborna.core.search.placeholder'); ?>" value="<?php echo $view->escape($events['filters']['search']); ?>">
        <span class="the-icon fa fa-search text-muted mt-xs"></span>
    </div>
    <?php if (isset($events['types']) && is_array($events['types'])) : ?>
        <div class="history-search panel-footer text-muted">
            <div class="row">
                <div class="col-sm-5">
                    <select name="includeEvents[]" multiple="multiple" class="form-control bdr-w-0" data-placeholder="<?php echo $view['translator']->trans('autoborna.lead.lead.filter.bundles.include.placeholder'); ?>">
                        <?php foreach ($events['types'] as $typeKey => $typeName) : ?>
                            <option value="<?php echo $view->escape($typeKey); ?>"<?php echo in_array($typeKey, $events['filters']['includeEvents']) ? ' selected' : ''; ?> >
                                <?php echo $typeName; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-5">
                    <select name="excludeEvents[]" multiple="multiple" class="form-control bdr-w-0" data-placeholder="<?php echo $view['translator']->trans('autoborna.lead.lead.filter.bundles.exclude.placeholder'); ?>">
                        <?php foreach ($events['types'] as $typeKey => $typeName) : ?>
                            <option value="<?php echo $view->escape($typeKey); ?>"<?php echo in_array($typeKey, $events['filters']['excludeEvents']) ? ' selected' : ''; ?> >
                                <?php echo $typeName; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <a class="btn btn-default btn-block" href="<?php echo $view['router']->url('autoborna_contact_timeline_export_action', ['leadId' => $lead->getId()]); ?>" data-toggle="download">
                        <span>
                            <i class="fa fa-download"></i> <span class="hidden-xs hidden-sm"><?php echo $view['translator']->trans('autoborna.core.export'); ?></span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <input type="hidden" name="leadId" id="leadId" value="<?php echo $view->escape($lead->getId()); ?>" />
</form>

<script>
    autobornaLang['showMore'] = '<?php echo $view['translator']->trans('autoborna.core.more.show'); ?>';
    autobornaLang['hideMore'] = '<?php echo $view['translator']->trans('autoborna.core.more.hide'); ?>';
</script>

<div id="timeline-table">
    <?php $view['slots']->output('_content'); ?>
</div>
