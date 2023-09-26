<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl) {
    $view->extend('AutobornaLeadBundle:Note:index.html.php');
}
?>

<ul class="notes" id="LeadNotes">
    <?php foreach ($notes as $note): ?>
        <?php
        //Use a separate layout for AJAX generated content
        echo $view->render('AutobornaLeadBundle:Note:note.html.php', [
            'note'        => $note,
            'lead'        => $lead,
            'permissions' => $permissions,
        ]); ?>
    <?php endforeach; ?>
</ul>
<?php if ($totalNotes = count($notes)): ?>
<div class="notes-pagination">
    <?php echo $view->render('AutobornaCoreBundle:Helper:pagination.html.php', [
        'totalItems' => $totalNotes,
        'target'     => '#notes-container',
        'page'       => $page,
        'limit'      => $limit,
        'sessionVar' => 'lead.'.$lead->getId().'.note',
        'baseUrl'    => $view['router']->path('autoborna_contactnote_index', ['leadId' => $lead->getId()]),
    ]); ?>
</div>
<?php endif; ?>