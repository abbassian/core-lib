<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use Autoborna\InstallBundle\Configurator\Step\CheckStep;

if ('index' == $tmpl) {
    $view->extend('AutobornaInstallBundle:Install:content.html.php');
}

?>
<div class="panel-heading">
    <h2 class="panel-title">
        <?php echo $view['translator']->trans('autoborna.install.heading.check.environment'); ?>
    </h2>
</div>
<div class="panel-body">
    <?php if (count($majors)) : ?>
    <div class="panel-group" id="minors">
        <div class="panel panel-danger">
            <div class="panel-heading">
                <h3 class="panel-title" style="font-size: 1em;">
                    <?php echo $view['translator']->trans('autoborna.install.heading.major.problems'); ?>
                </h3>
            </div>
            <div class="panel-body alert-warning">
                <p><?php echo $view['translator']->trans('autoborna.install.sentence.major.problems', ['%majors%' => count($majors)]); ?></p>
            </div>
            <ul class="list-group">
                <?php foreach ($majors as $message) : ?>
                    <?php switch ($message) :
                        case 'autoborna.install.cache.unwritable': ?>
                            <li class="list-group-item"><?php echo $view['translator']->trans('autoborna.install.directory.unwritable', ['%path%' => $cacheDir]); ?></li>
                            <?php break;
                        case 'autoborna.install.config.unwritable': ?>
                            <li class="list-group-item"><?php echo $view['translator']->trans($message, ['%path%' => $configFile]); ?></li>
                            <?php break;
                        case 'autoborna.install.logs.unwritable': ?>
                            <li class="list-group-item"><?php echo $view['translator']->trans('autoborna.install.directory.unwritable', ['%path%' => $logDir]); ?></li>
                            <?php break;
                        case 'autoborna.install.apc.version': ?>
                            <?php $minAPCverison = version_compare(PHP_VERSION, '5.4.0', '>=') ? '3.1.13' : '3.0.17'; ?>
                            <li class="list-group-item"><?php echo $view['translator']->trans($message, ['%minapc%' => $minAPCverison, '%currentapc%' => phpversion('apc')]); ?></li>
                            <?php break;
                        default: ?>
                            <li class="list-group-item"><?php echo $view['translator']->trans($message); ?></li>
                            <?php break;
                    endswitch; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!count($majors)) : ?>
        <div class="alert alert-success">
            <h4><i class="fa fa-check"></i> <?php echo $view['translator']->trans('autoborna.install.heading.ready'); ?></h4>
            <p><?php echo $view['translator']->trans('autoborna.install.sentence.ready'); ?></p>
        </div>
    <?php endif; ?>


    <?php if (count($minors)) : ?>
    <div class="panel-group" id="minors">
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title" style="font-size: 1em;">
                    <a data-toggle="collapse" style="display: block; " href="#minorDetails"><i class="fa fa-chevron-down"></i> <?php echo $view['translator']->trans('autoborna.install.heading.minor.problems'); ?></a>
                </h3>
            </div>
            <div id="minorDetails" class="panel-collapse collapse">
                <div class="panel-body alert-warning">
                    <p><?php echo $view['translator']->trans('autoborna.install.sentence.minor.problems'); ?></p>
                </div>
                 <ul class="list-group">
                    <?php foreach ($minors as $message) : ?>
                        <?php switch ($message) :
                            case 'autoborna.install.pcre.version': ?>
                                <li class="list-group-item"><?php echo $view['translator']->trans($message, ['%pcreversion%' => (float) PCRE_VERSION]); ?></li>
                                <?php break;
                            case 'autoborna.install.php.version.not.supported': ?>
                                <li class="list-group-item"><?php echo $view['translator']->trans($message, ['%phpversion%' => PHP_VERSION]); ?></li>
                                <?php break;
                            case 'autoborna.install.php.version.has.only.security.support': ?>
                                <li class="list-group-item"><?php echo $view['translator']->trans($message, ['%phpversion%' => PHP_VERSION]); ?></li>
                                <?php break;
                            case 'autoborna.install.memory.limit': ?>
                                <li class="list-group-item"><?php echo $view['translator']->trans($message, ['%min_memory_limit%' => CheckStep::$memory_limit]); ?></li>
                                <?php break;
                            default: ?>
                                <li class="list-group-item"><?php echo $view['translator']->trans($message); ?></li>
                                <?php break; ?>
                        <?php endswitch; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!(count($majors))): ?>
    <?php echo $view['form']->start($form); ?>

        <div class="row mt-20">
            <div class="col-sm-12">
                <?php echo $view['form']->row($form['buttons']); ?>
            </div>
        </div>

    <?php echo $view['form']->end($form); ?>
    <?php endif; ?>
</div>
