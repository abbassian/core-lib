<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('AutobornaCoreBundle:Default:content.html.php');
$view['slots']->set('autobornaContent', 'leadfield');
$userId = $form->vars['data']->getId();
if (!empty($userId)) {
    $isNew  = false;
    $field  = $form->vars['data']->getLabel();
    $header = $view['translator']->trans('autoborna.lead.field.header.edit', ['%name%' => $field]);
} else {
    $isNew  = true;
    $header = $view['translator']->trans('autoborna.lead.field.header.new');
}
$view['slots']->set('headerTitle', $header);

// Render the templates so they don't get rendered automatically
$textareaTemplate        = $view['form']->row($form['properties_textarea_template']);
$selectTemplate          = $view['form']->row($form['properties_select_template']);
$lookupTemplate          = $view['form']->row($form['properties_lookup_template']);
$defaultTextTemplate     = $view['form']->widget($form['default_template_text']);
$defaultTextareaTemplate = $view['form']->widget($form['default_template_textarea']);
$defaultLocaleTemplate   = $view['form']->widget($form['default_template_locale']);
$defaultSelectTemplate   = $view['form']->widget($form['default_template_select']);
$defaultBoolTemplate     = $view['form']->widget($form['default_template_boolean']);
$defaultCountryTemplate  = $view['form']->widget($form['default_template_country']);
$defaultRegionTemplate   = $view['form']->widget($form['default_template_region']);
$defaultTimezoneTemplate = $view['form']->widget($form['default_template_timezone']);
?>

<?php echo $view['form']->start($form); ?>
<div class="box-layout">
    <!-- container -->
    <div class="col-md-8 bg-auto height-auto bdr-r">
        <div class="pa-md">
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['label']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['alias']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['object']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['group']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['type']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['defaultValue']); ?>
                </div>
            </div>

            <?php
            $type          = $form['type']->vars['data'];
            $properties    = $form['properties']->vars['data'];
            $errors        = count($form['properties']->vars['errors']);
            $feedbackClass = (!empty($errors)) ? ' has-error' : '';
            ?>
            <div class="row">
                <div class="form-group col-md-6<?php echo $feedbackClass; ?>">
                    <div id="leadfield_properties">
                        <?php
                        switch ($type):
                        case 'boolean':
                            echo $view->render('AutobornaLeadBundle:Field:properties_boolean.html.php', [
                                'yes' => isset($properties['yes']) ? $properties['yes'] : '',
                                'no'  => isset($properties['no']) ? $properties['no'] : '',
                            ]);
                            break;
                        case 'number':
                            echo $view->render('AutobornaLeadBundle:Field:properties_number.html.php', [
                                'roundMode' => isset($properties['roundmode']) ? $properties['roundmode'] : '',
                                'scale'     => isset($properties['scale']) ? $properties['scale'] : '',
                            ]);
                            break;
                        case 'select':
                            case 'multiselect':
                            echo $view->render('AutobornaLeadBundle:Field:properties_select.html.php', [
                                'form'           => $form['properties'],
                                'selectTemplate' => $selectTemplate,
                            ]);
                            break;
                        case 'lookup':
                            echo $view->render('AutobornaLeadBundle:Field:properties_select.html.php', [
                                'form'           => $form['properties'],
                                'selectTemplate' => $lookupTemplate,
                                'type'           => 'lookup',
                            ]);
                                break;
                        case 'textarea':
                            echo $view->render('AutobornaLeadBundle:Field:properties_textarea.html.php', [
                                'form'             => $form['properties'],
                                'textareaTemplate' => $textareaTemplate,
                            ]);
                                break;
                        endswitch;
                        ?>
                    </div>
                    <?php echo $view['form']->errors($form['properties']); ?>
                </div>
            </div>
            <?php $form['properties']->setRendered(); ?>
        </div>
    </div>
    <div class="col-md-4 bg-white height-auto">
        <div class="pr-lg pl-lg pt-md pb-md">
            <div class="row">
                <div class="col-md-12">
                    <?php echo $view['form']->row($form['order']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isPublished']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isRequired']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isVisible']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isShortVisible']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isListable']); ?>
                </div>
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isPubliclyUpdatable']); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?php echo $view['form']->row($form['isUniqueIdentifer']); ?>
                </div>
            </div>
            <div class="row unique-identifier-warning" style="<?php if (!$form['isUniqueIdentifer']->vars['data']): echo 'display:none;'; endif; ?>">
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        <?php echo $view['translator']->trans('autoborna.lead.field.form.isuniqueidentifer.warning'); ?>
                    </div>
                </div>
            </div>
            <?php echo $view['form']->rest($form); ?>
        </div>
    </div>
</div>
<?php echo $view['form']->end($form); ?>

<?php if ($isNew): ?>
<div id="field-templates" class="hide">
    <div class="default_template_text">
        <?php echo $defaultTextTemplate; ?>
    </div>
    <div class="default_template_textarea">
        <?php echo $defaultTextareaTemplate; ?>
    </div>
    <div class="default_template_boolean">
        <?php echo $defaultBoolTemplate; ?>
    </div>
    <div class="default_template_country">
        <?php echo $defaultCountryTemplate; ?>
    </div>
    <div class="default_template_region">
        <?php echo $defaultRegionTemplate; ?>
    </div>
    <div class="default_template_locale">
        <?php echo $defaultLocaleTemplate; ?>
    </div>
    <div class="default_template_timezone">
        <?php echo $defaultTimezoneTemplate; ?>
    </div>
    <div class="default_template_select">
        <?php echo $defaultSelectTemplate; ?>
    </div>
<?php
    echo $view->render('AutobornaLeadBundle:Field:properties_number.html.php');
    echo $view->render('AutobornaLeadBundle:Field:properties_boolean.html.php');
    echo $view->render('AutobornaLeadBundle:Field:properties_select.html.php', [
        'selectTemplate' => $selectTemplate,
    ]);
    echo $view->render('AutobornaLeadBundle:Field:properties_select.html.php', [
        'selectTemplate' => $selectTemplate,
        'type'           => 'multiselect',
    ]);
    echo $view->render('AutobornaLeadBundle:Field:properties_select.html.php', [
        'selectTemplate' => $lookupTemplate,
        'type'           => 'lookup',
    ]);
    echo $view->render('AutobornaLeadBundle:Field:properties_textarea.html.php', [
        'textareaTemplate' => $textareaTemplate,
    ]);
?>
</div>
<?php endif; ?>
