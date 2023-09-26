<?php

echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
    'sessionVar' => 'lead',
    'orderBy'    => 'l.city, l.state',
    'text'       => 'autoborna.lead.lead.thead.location',
    'class'      => 'col-lead-location '.$class,
]);
