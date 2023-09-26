<?php

/*
 * @copyright   2016 Autoborna Contributors. All rights reserved
 * @author      Autoborna, Inc.
 *
 * @link        https://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$hasErrors     = count($form->vars['errors']);
$feedbackClass = (!empty($hasErrors)) ? ' has-error' : '';
$field         = $form->vars['name'];
$hide          = (!empty($fieldValue)) ? '' : ' hide';
$filename      = \Autoborna\CoreBundle\Helper\InputHelper::alphanum($view['translator']->trans($form->vars['label']), true, '_');
$downloadUrl   = $view['router']->path('autoborna_config_action',
    [
        'objectAction' => 'download',
        'objectId'     => $field,
        'filename'     => $filename,
    ]
);
$removeUrl = $view['router']->path('autoborna_config_action',
    [
        'objectAction' => 'remove',
        'objectId'     => $field,
    ]
);
?>
<div class="row">
    <div class="form-group col-xs-12 <?php echo $feedbackClass; ?>">
        <?php echo $view['form']->label($form, $form->vars['label']); ?>
        <span class="small pull-right<?php echo $hide; ?>">
            <a
               data-toggle="confirmation"
               href="<?php echo $removeUrl; ?>"
               data-message="<?php echo $view->escape($view['translator']->trans('autoborna.config.remove_file_contents')); ?>"
               data-confirm-text="<?php echo $view->escape($view['translator']->trans('autoborna.core.remove')); ?>"
               data-confirm-callback="removeConfigValue"
               data-cancel-text="<?php echo $view->escape($view['translator']->trans('autoborna.core.form.cancel')); ?>">
                <?php echo $view['translator']->trans('autoborna.core.remove'); ?>
            </a>
            <span> | </span>
            <a href="<?php echo $downloadUrl; ?>"><?php echo $view['translator']->trans('autoborna.core.download'); ?></a>
        </span>
        <?php echo $view['form']->widget($form); ?>
        <?php echo $view['form']->errors($form); ?>
    </div>
</div>