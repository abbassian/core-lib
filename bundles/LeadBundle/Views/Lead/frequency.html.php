<?php

/*
 * @copyright   2016 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>

    <?php echo $view['form']->start($form); ?>
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#channels"><?php echo $view['translator']->trans('autoborna.lead.contact.channels'); ?></a></li>
    <li><a data-toggle="tab" href="#categories"><?php echo $view['translator']->trans('autoborna.lead.preferred.categories'); ?></a></li>
    <li><a data-toggle="tab" href="#segments"><?php echo $view['translator']->trans('autoborna.lead.preferred.segments'); ?></a></li>
</ul>

<div class="tab-content">
    <div id="channels" class="tab-pane fade in active">
        <table class="table" width="100%">
            <thead >
            <tr >
                <th>
                    <input type="checkbox" id="lead_contact_frequency_rules_subscribed_channels_0" name="check_all"
                           onclick="Autoborna.togglePreferredChannel('all');" value="all">
                </th>
                <th>
                    <?php echo $view['translator']->trans('autoborna.lead.contact.channels'); ?>
                </th>
                <th><?php echo $view['translator']->trans('autoborna.lead.preferred.frequency'); ?></th>
                <th><?php echo $view['translator']->trans('autoborna.lead.preferred.channels'); ?></th>
            </tr>
            </thead>
            <tbody >

            <?php foreach ($form['lead_channels']['subscribed_channels']->vars['choices'] as $channel): ?>
                <?php
                $contactMe     = isset($leadChannels[$channel->value]);
                $isContactable = $contactMe ? '' : 'text-muted';
                $hidden        = $contactMe ? '' : 'hide';
                $checked       = $contactMe ? 'checked' : '';
                $disabled      = isset($leadChannels[$channel->value]) ? '' : 'disabled';
                ?>
                <tr>
                    <th style="vertical-align: top" class="col-md-1">
                        <input type="checkbox" id="<?php echo $channel->value; ?>"
                               name="lead_contact_frequency_rules[lead_channels][subscribed_channels][]" class="control-label"
                               onclick="Autoborna.togglePreferredChannel(this.value);"
                               value="<?php echo $view->escape($channel->value); ?>" <?php echo $checked; ?>>
                    </th>
                    <td class="col-md-1" style="vertical-align: top">
                        <div id="is-contactable-<?php echo $channel->value; ?>" class="<?php echo $isContactable; ?> fw-sb">
                            <?php echo $view['channel']->getChannelLabel($channel->value); ?>
                        </div>
                    </td>
                    <td class="col-md-9" style="vertical-align: top">
                            <div>
                                <div class="pull-left">
                                    <?php
                                    $attr = $form['lead_channels']['frequency_number_'.$channel->value]->vars['attr'];
                                    $attr['class'] .= ' pull-left';
                                    ?>
                                    <?php echo $view['form']->widget($form['lead_channels']['frequency_number_'.$channel->value], ['attr' => $attr]); ?>
                                    <?php echo $view['form']->label($form['lead_channels']['frequency_time_'.$channel->value]); ?>
                                    <div class="frequency-select"><?php echo $view['form']->widget($form['lead_channels']['frequency_time_'.$channel->value]); ?></div>
                                </div>
                            </div>
                    </td>
                <td class="col-md-1" style="vertical-align: top;" align="center">
                        <input type="radio" id="preferred_<?php echo $channel->value; ?>"
                               name="lead_contact_frequency_rules[lead_channels][preferred_channel]" class="contact"
                               value="<?php echo $view->escape($channel->value); ?>" <?php if ($form['lead_channels']['preferred_channel']->vars['value'] == $channel->value) {
                                        echo $checked;
                                    } ?> <?php echo $disabled; ?>>

                </td>
                </tr>
                <tr style="border-top:none"><th style="border-top:none"></th>
                    <td  style="border-top:none"></td>
                    <td colspan="2" style="border-top:none">
                        <div id="frequency_<?php echo $channel->value; ?>" <?php if (!empty($hidden)) :?>class="<?php echo $hidden; ?>"<?php endif; ?> >
                            <div>
                                <label class="text-muted fw-n"><?php echo $view['translator']->trans('autoborna.lead.frequency.dates.label'); ?></label>
                            </div>
                            <div>
                                <?php echo $view['form']->widget($form['lead_channels']['contact_pause_start_date_'.$channel->value]); ?>
                                <div style="float:left;">
                                    <?php echo $view['form']->label($form['lead_channels']['contact_pause_end_date_'.$channel->value]); ?>
                                </div>
                                    <?php echo $view['form']->widget($form['lead_channels']['contact_pause_end_date_'.$channel->value]); ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div id="categories" class="tab-pane fade">
        <?php
        unset($form['lead_channels']['preferred_channel']);
        unset($form['lead_channels']['subscribed_channels']); ?>

        <table class="table" width="100%">
            <thead >
            <tr >
                <th>
                <?php echo $view['form']->row($form['global_categories']); ?>
                </th>
            </tr>
            </thead>
        </table>

    </div>
    <div id="segments" class="tab-pane fade">
        <table class="table" width="100%">
            <thead >
            <tr >
                <th>
                    <?php echo $view['form']->row($form['lead_lists']); ?>
                </th>
            </tr>
            </thead>
        </table>
    </div>
</div>
<?php echo $view['form']->end($form); ?>
