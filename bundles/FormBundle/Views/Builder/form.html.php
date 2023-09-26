<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
/** @var \Autoborna\FormBundle\Entity\Form $form */
$formName = '_'.$form->generateFormName().(isset($suffix) ? $suffix : '');
if (!isset($fields)) {
    $fields = $form->getFields();
}
$pageCount = 1;

if (!isset($inBuilder)) {
    $inBuilder = false;
}

if (!isset($action)) {
    $action = $view['router']->url('autoborna_form_postresults', ['formId' => $form->getId()]);
}

if (!isset($theme)) {
    $theme = '';
}

if (!isset($contactFields)) {
    $contactFields = $companyFields = [];
}

if (!isset($style)) {
    $style = '';
}

if (!isset($isAjax)) {
    $isAjax = true;
}

if (!isset($submissions)) {
    $submissions = null;
}

if (!isset($lead)) {
    $lead = null;
}
?>

<?php echo $style; ?>
<style type="text/css" scoped>
    .autobornaform-field-hidden { display:none }
</style>

<div id="autobornaform_wrapper<?php echo $formName; ?>" class="autobornaform_wrapper">
    <form autocomplete="false" role="form" method="post" action="<?php echo  $action; ?>" id="autobornaform<?php echo $formName; ?>" <?php if ($isAjax): ?> data-autoborna-form="<?php echo ltrim($formName, '_'); ?>"<?php endif; ?> enctype="multipart/form-data" <?php echo $form->getFormAttributes(); ?>>
        <div class="autobornaform-error" id="autobornaform<?php echo $formName; ?>_error"></div>
        <div class="autobornaform-message" id="autobornaform<?php echo $formName; ?>_message"></div>
        <div class="autobornaform-innerform">
            <?php
            $displayManager = new \Autoborna\FormBundle\ProgressiveProfiling\DisplayManager(
                $form,
                !empty($viewOnlyFields) ? $viewOnlyFields : []
            );
            /** @var \Autoborna\FormBundle\Entity\Field $f */
            foreach ($fields as $fieldId => $f):
                if (isset($formPages['open'][$fieldId])):
                    // Start a new page
                    $lastFieldAttribute = ($lastFormPage === $fieldId) ? ' data-autoborna-form-pagebreak-lastpage="true"' : '';
                    echo "\n          <div class=\"autobornaform-page-wrapper autobornaform-page-$pageCount\" data-autoborna-form-page=\"$pageCount\"$lastFieldAttribute>\n";
                endif;

                if (!$f->getParent() && $f->showForContact($submissions, $lead, $form, $displayManager)):
                    if ($f->isCustom()):
                        if (!isset($fieldSettings[$f->getType()])):
                            continue;
                        endif;
                        $params = $fieldSettings[$f->getType()];
                        $f->setCustomParameters($params);

                        $template = $params['template'];
                    else:
                        if (!$f->isAlwaysDisplay() && !$f->getShowWhenValueExists() && $f->getLeadField() && $f->getIsAutoFill() && $lead && !empty($lead->getFieldValue($f->getLeadField()))) {
                            $f->setType('hidden');
                        } else {
                            $displayManager->increaseDisplayedFields($f);
                        }
                        $template = 'AutobornaFormBundle:Field:'.$f->getType().'.html.php';
                    endif;

                    echo $view->render(
                        $theme.$template,
                        [
                            'field'         => $f->convertToArray(),
                            'id'            => $f->getAlias(),
                            'formName'      => $formName,
                            'fieldPage'     => ($pageCount - 1), // current page,
                            'contactFields' => $contactFields,
                            'companyFields' => $companyFields,
                            'inBuilder'     => $inBuilder,
                            'fields'        => $fields,
                        ]
                    );
                endif;
                $parentField = $f;
                foreach ($fields as $fieldId2 => $f):
                    if ('hidden' !== $parentField->getType() && $f->getParent() == $parentField->getId()):
                    if ($f->isCustom()):
                        if (!isset($fieldSettings[$f->getType()])):
                            continue;
                        endif;
                        $params = $fieldSettings[$f->getType()];
                        $f->setCustomParameters($params);

                        $template = $params['template'];
                    else:
                        $template = 'AutobornaFormBundle:Field:'.$f->getType().'.html.php';
                    endif;

                    echo $view->render(
                        $theme.$template,
                        [
                            'field'         => $f->convertToArray(),
                            'id'            => $f->getAlias(),
                            'formName'      => $formName,
                            'fieldPage'     => ($pageCount - 1), // current page,
                            'contactFields' => $contactFields,
                            'companyFields' => $companyFields,
                            'inBuilder'     => $inBuilder,
                            'fields'        => $fields,
                        ]
                    );
                    endif;
                endforeach;

                if (isset($formPages) && isset($formPages['close'][$fieldId])):
                    // Close the page
                    echo "\n            </div>\n";
                    ++$pageCount;
                endif;

            endforeach;
            ?>
        </div>

        <input type="hidden" name="autobornaform[formId]" id="autobornaform<?php echo $formName; ?>_id" value="<?php echo $view->escape($form->getId()); ?>"/>
        <input type="hidden" name="autobornaform[return]" id="autobornaform<?php echo $formName; ?>_return" value=""/>
        <input type="hidden" name="autobornaform[formName]" id="autobornaform<?php echo $formName; ?>_name" value="<?php echo $view->escape(ltrim($formName, '_')); ?>"/>

        <?php echo (isset($formExtra)) ? $formExtra : ''; ?>
</form>
</div>
