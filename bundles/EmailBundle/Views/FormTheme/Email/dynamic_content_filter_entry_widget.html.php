<?php

/*
 * @copyright   2015 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<div class="tab-pane dynamic-content-filter bdr-w-0" id="<?php echo $form->vars['id']; ?>">
    <div class="row form-group">
        <div class="col-xs-10">
            <?php echo $view['form']->label($form['content']); ?>
        </div>
        <div class="col-xs-2">
            <?php if ('emailform_dynamicContent_0_filters_0' !== $id) : ?>
            <a class="remove-item remove-filter btn btn-default text-danger"><i class="fa fa-trash-o"></i></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-xs-12">
            <?php echo $view['form']->widget($form['content']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-7">
            <label><?php echo $view['translator']->trans('Filters'); ?></label>
        </div>
        <div class="col-xs-5">
            <div class="form-group">
                <div class="available-filters pl-0">
                    <select class="chosen form-control" data-autoborna="available_filters">
                        <option value=""></option>
                        <?php
                        foreach ($fields as $object => $field):
                            $header = $object;
                            $icon   = ('company' == $object) ? 'fa-building' : 'fa-user';
                            ?>
                            <optgroup label="<?php echo $view['translator']->trans('autoborna.lead.'.$header); ?>">
                                <?php foreach ($field as $value => $params):
                                    $list      = (!empty($params['properties']['list'])) ? $params['properties']['list'] : [];
                                    $choices   = ('boolean' === $params['properties']['type'])
                                        ?
                                        \Autoborna\LeadBundle\Helper\FormFieldHelper::parseBooleanList($list)
                                        :
                                        \Autoborna\LeadBundle\Helper\FormFieldHelper::parseListForChoices($list);
                                    $list      = json_encode($choices);
                                    $callback  = (!empty($params['properties']['callback'])) ? $params['properties']['callback'] : '';
                                    $operators = (!empty($params['operators'])) ? $view->escape(json_encode($params['operators'])) : '{}';
                                    ?>
                                    <option value="<?php echo $view->escape($value); ?>" data-autoborna="available_<?php echo $value; ?>" data-field-object="<?php echo $object; ?>" data-field-type="<?php echo $params['properties']['type']; ?>" data-field-list="<?php echo $view->escape($list); ?>" data-field-callback="<?php echo $callback; ?>" data-field-operators='<?php echo $operators; ?>' class="segment-filter fa <?php echo $icon; ?>"><?php echo $view['translator']->trans($params['label']); ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div data-filter-container data-index="<?php echo count($form['filters']); ?>">
    <?php
    foreach ($form['filters'] as $i => $filter) {
        $isPrototype = ('__name__' == $filter->vars['name']);
        if ($isPrototype || isset($form->vars['fields'][$filter->vars['value']['object']][$filter->vars['value']['field']])) {
            echo $view['form']->widget($filter, ['first' => (0 === $i)]);
        }
    }
    ?>
    </div>
</div>
