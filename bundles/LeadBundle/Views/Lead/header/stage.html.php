<?php

echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
    'sessionVar' => 'lead',
    'orderBy'    => 'l.stage_id',
    'text'       => 'autoborna.lead.stage.label',
    'class'      => 'col-lead-stage '.$class,
]);
