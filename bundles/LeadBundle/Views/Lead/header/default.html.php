<?php

echo $view->render('AutobornaCoreBundle:Helper:tableheader.html.php', [
    'sessionVar' => 'lead',
    'orderBy'    => 'l.'.$column,
    'text'       => $label,
    'class'      => 'col-lead-'.$column.' '.$class,
]);
