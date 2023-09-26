<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if ('index' == $tmpl) :
$view->extend('AutobornaCoreBundle:Default:content.html.php');
$view['slots']->set('autobornaContent', 'asset');
$view['slots']->set('headerTitle', $view['translator']->trans('autoborna.asset.remote.file.browse'));
?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
    <div class="page-list">
<?php endif; ?>
        <?php if (count($integrations)): ?>
            <!-- start: box layout -->
            <div class="box-layout">
                       <!-- step container -->
                <div class="col-md-3 bg-white">
                    <div class="pt-md pr-md pb-md">
                        <ul class="list-group list-group-tabs">
                            <?php $step = 1; ?>
                            <?php /** @var \Autoborna\PluginBundle\Integration\AbstractIntegration $integration */ ?>
                            <?php foreach ($integrations as $integration): ?>
                                <li class="list-group-item<?php if (1 === $step) {
    echo ' active';
} ?>" id="tab<?php echo $integration->getName(); ?>">
                                    <a href="#" class="steps" onclick="Autoborna.updateRemoteBrowser('<?php echo $integration->getName(); ?>');">
                                        <?php echo $integration->getDisplayName(); ?>
                                    </a>
                                </li>
                                <?php ++$step; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <!--/ step container -->

                <!-- container -->
                <div class="col-md-9 bg-auto bdr-l">
                    <div id="remoteFileBrowser">
                        <div class="alert alert-warning col-md-6 col-md-offset-3 mt-md">
                            <p><?php echo $view['translator']->trans('autoborna.asset.remote.select_service'); ?></p>
                        </div>
                    </div>
                </div>
                <!--/ end: container -->
            </div>
            <!--/ end: box layout -->
        <?php endif; ?>
<?php if ('index' == $tmpl) : ?>
    </div>
</div>
<?php endif; ?>
