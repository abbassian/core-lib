<?php

echo $view->render(
    'AutobornaFormBundle:Field:select.html.php',
    [
        'field'    => $field,
        'fields'   => isset($fields) ? $fields : [],
        'inForm'   => (isset($inForm)) ? $inForm : false,
        'list'     => \Autoborna\LeadBundle\Helper\FormFieldHelper::getCountryChoices(),
        'id'       => $id,
        'formId'   => (isset($formId)) ? $formId : 0,
        'formName' => (isset($formName)) ? $formName : '',
    ]
);
