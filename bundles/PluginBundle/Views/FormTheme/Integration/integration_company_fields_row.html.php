<?php

$containerId    = 'companyFieldsContainer';
$numberOfFields = ($form->offsetExists('update_autoborna_company1')) ? 5 : 4;
$object         = 'company';

include __DIR__.'/fields_row.html.php';
