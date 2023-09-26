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

$objectName = $view['translator']->trans($objectName);

$view['slots']->set('autobornaContent', 'leadImport');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.lead.import.leads', ['%object%' => $objectName]));

?>
<?php if (isset($step) && \Autoborna\LeadBundle\Controller\ImportController::STEP_UPLOAD_CSV === $step): ?>
<?php echo $view->render('AutobornaLeadBundle:Import:upload_form.html.php', ['form' => $form]); ?>
<?php else: ?>
<?php echo $view->render('AutobornaLeadBundle:Import:mapping_form.html.php', ['form' => $form]); ?>
<?php endif; ?>
