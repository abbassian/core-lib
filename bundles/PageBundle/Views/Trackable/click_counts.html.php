<?php if (!empty($trackables)): ?>
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered click-list">
            <thead>
            <tr>
                <td><?php echo $view['translator']->trans('autoborna.trackable.click_url'); ?></td>
                <td><?php echo $view['translator']->trans('autoborna.trackable.click_count'); ?></td>
                <td><?php echo $view['translator']->trans('autoborna.trackable.click_unique_count'); ?></td>
                <?php echo $view['content']->getCustomContent('click_counts_headers', $autobornaTemplateVars); ?>
                <td><?php echo $view['translator']->trans('autoborna.trackable.click_track_id'); ?></td>
            </tr>
            </thead>
            <tbody>
                <?php
                    $totalClicks       = 0;
                    $totalUniqueClicks = 0;
                    foreach ($trackables as $link):
                        $totalClicks += $link['hits'];
                        $totalUniqueClicks += $link['unique_hits'];
                        ?>
                        <tr>
                            <td class="long-text"><a href="<?php echo $link['url']; ?>"><?php echo $link['url']; ?></a></td>
                            <td class="text-center"><?php echo $link['hits']; ?></td>
                            <td class="text-center">
                                <span class="mt-xs label label-primary has-click-event clickable-stat">
                        <?php if (isset($channel) && isset($entity)): ?>
                            <a href="<?php echo $view['router']->path(
                                'autoborna_contact_index',
                                ['search' => $view['translator']->trans('autoborna.lead.lead.searchcommand.page_source').':'.$channel.' '.$view['translator']->trans('autoborna.lead.lead.searchcommand.page_source_id').':'.$entity->getId().' '.$view['translator']->trans('autoborna.lead.lead.searchcommand.page_id').':'.$link['id']]
                            ); ?>" data-toggle="tooltip"
                               title="<?php echo $view['translator']->trans('autoborna.email.stat.simple.tooltip'); ?>">
                               <?php echo $link['unique_hits']; ?>
                            </a>
                        <?php else: ?>
                            <?php echo $link['unique_hits']; ?>
                        <?php endif; ?>
                        </span>
                            </td>
                            <?php echo $view['content']->getCustomContent('click_counts', array_merge($autobornaTemplateVars, ['redirect_id' => $link['redirect_id']])); ?>
                            <td><?php echo $link['redirect_id']; ?></td>
                        </tr>
                <?php endforeach; ?>

                <tr>
                    <td class="long-text"><?php echo $view['translator']->trans('autoborna.trackable.total_clicks'); ?></td>
                    <td class="text-center"><?php echo $totalClicks; ?></td>
                    <td class="text-center">
                        <span class="mt-xs label label-primary has-click-event clickable-stat">
                  <?php if (isset($channel) && isset($entity)): ?>
                      <a href="<?php echo $view['router']->path(
                          'autoborna_contact_index',
                          ['search' => $view['translator']->trans('autoborna.lead.lead.searchcommand.page_source').':'.$channel.' '.$view['translator']->trans('autoborna.lead.lead.searchcommand.page_source_id').':'.$entity->getId()]
                      ); ?>" data-toggle="tooltip"
                         title="<?php echo $view['translator']->trans('autoborna.email.stat.simple.tooltip'); ?>">
                        <?php echo $totalUniqueClicks; ?>
                            </a>
                  <?php else: ?>
                      <?php echo $totalUniqueClicks; ?>
                  <?php endif; ?>
                        </span>
                    </td>
                    <td></td>
                </tr>

            </tbody>
        </table>
    </div>
<?php else: ?>
    <?php echo $view->render(
        'AutobornaCoreBundle:Helper:noresults.html.php',
        [
            'header'  => 'autoborna.trackable.click_counts.header_none',
            'message' => 'autoborna.trackable.click_counts.none',
        ]
    ); ?>
    <div class="clearfix"></div>
<?php endif; ?>