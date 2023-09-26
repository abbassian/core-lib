<?php

$view->extend('AutobornaCoreBundle:Default:slim.html.php');
$js = <<<JS
Autoborna.handleIntegrationCallback("$integration", "$csrfToken", "$code", "$callbackUrl", "$clientIdKey", "$clientSecretKey");
JS;
$view['assets']->addScriptDeclaration($js, 'bodyClose');
