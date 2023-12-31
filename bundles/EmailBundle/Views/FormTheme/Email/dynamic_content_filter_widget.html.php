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

<div class="tab-pane dynamic-content bdr-w-0<?php echo '0' === $form->vars['name'] ? ' active' : ' fade'; ?>" id="<?php echo $form->vars['id']; ?>">
    <div class="panel">
        <?php
        $tabHtml = '<ul class="bg-auto nav nav-tabs pr-md pl-md" id="dynamicContentFilterTabs_'.$form->vars['name'].'">';
        $tabHtml .= '<li><a role="tab" class="addNewDynamicContentFilter" data-index="'.$form->vars['name']
            .'"><i class="fa fa-plus text-success"></i> '.$view['translator']->trans('autoborna.core.form.new').'</a></li>';
        $tabHtml .= '<li class="active"><a data-toggle="tab" href="#emailform_dynamicContent_'.$form->vars['name'].'_default" role="tab">Default</a></li>';
        $tabContentHtml = '<div class="tab-content pa-md"><div class="tab-pane bdr-w-0 active" id="emailform_dynamicContent_'.$form->vars['name'].'_default">';

        $tabContentHtml .= '<div class="row hidden"><div class="col-xs-10">';
        $tabContentHtml .= $view['form']->row($form['tokenName']);
        $tabContentHtml .= '</div></div>';

        $tabContentHtml .= $view['form']->row($form['content']);
        $tabContentHtml .= '</div>';

        foreach ($form['filters'] as $i => $filter) {
            $isFirst = 0 === $i ? ' active' : '';
            $tabHtml .= '<li><a role="tab" data-toggle="tab" href="#'.$filter->vars['id'].'">'.$view['translator']->trans('autoborna.core.dynamicContent.tab').' '.($i + 1).'</a></li>';

            $tabContentHtml .= $view['form']->widget($filter);
        }

        $tabHtml .= '</ul>';
        $tabContentHtml .= '</div>';

        echo $tabHtml;
        echo $tabContentHtml;
        ?>
    </div>
</div>
