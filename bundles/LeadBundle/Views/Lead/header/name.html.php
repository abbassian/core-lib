<?php

echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
    'sessionVar' => 'lead',
    'orderBy'    => 'l.lastname, l.firstname, l.company, l.email',
    'text'       => 'autoborna.core.name',
    'class'      => 'col-lead-name '.$class,
]);
