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

$view['slots']->set('autobornaContent', 'emailSend');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.email.send.list', ['%name%' => $email->getName()]));

?>
<div class="row">
    <div class="col-sm-offset-3 col-sm-6">
        <div class="ml-lg mr-lg mt-md pa-lg">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <div class="panel-title">
                        <p><?php echo $view['translator']->trans('autoborna.email.send.instructions'); ?></p>
                    </div>
                </div>
                <div class="panel-body">
                    <?php echo $view['form']->start($form); ?>
                    <div class="col-xs-8 col-xs-offset-2">
                        <div class="well mt-lg">
                            <div class="input-group">
                                <?php echo $view['form']->widget($form['batchlimit']); ?>
                                <span class="input-group-btn">
                                    <?php echo $view->render('AutobornaCoreBundle:Helper:confirm.html.php', [
                                        'message'         => $view['translator']->trans('autoborna.email.form.confirmsend', ['%name%' => $email->getName()]),
                                        'confirmText'     => $view['translator']->trans('autoborna.email.send'),
                                        'confirmCallback' => 'submitSendForm',
                                        'iconClass'       => 'fa fa-send-o',
                                        'btnText'         => $view['translator']->trans('autoborna.email.send'),
                                        'btnClass'        => 'btn btn-primary btn-send'.((!$pending) ? ' disabled' : ''),
                                    ]);
                                    ?>
                                </span>
                            </div>
                            <?php echo $view['form']->errors($form['batchlimit']); ?>
                            <div class="text-center">
                                <span class="label label-primary mt-lg"><?php echo $view['translator']->trans(
                                        'autoborna.email.send.pending',
                                        ['%count%' => $pending]
                                    ); ?></span>
                                <div class="mt-sm">
                                    <a class="text-danger mt-md" href="<?php echo $view['router']->path('autoborna_email_action', ['objectAction' => 'view', 'objectId' => $email->getId()]); ?>" data-toggle="ajax"><?php echo $view['translator']->trans('autoborna.core.form.cancel'); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo $view['form']->end($form); ?>
                </div>
            </div>
        </div>
    </div>
</div>
