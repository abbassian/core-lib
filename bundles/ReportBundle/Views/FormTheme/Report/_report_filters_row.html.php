<?php
$hasErrors     = count($form->vars['errors']);
$feedbackClass = (!empty($hasErrors)) ? ' has-error' : '';
?>
<div id="filterSelectorContainer" class="row">
    <div class="form-group col-md-12<?php echo $feedbackClass; ?>">
        <?php echo $view['form']->widget($form); ?>
        <?php echo $view['form']->errors($form); ?>
    </div>
    <div class="col-xs-12">
        <button type="button" class="btn btn-primary" onclick="Autoborna.addReportRow('report_filters');"><?php echo $view['translator']->trans('autoborna.report.report.label.addfilter'); ?></button>
    </div>
</div>
