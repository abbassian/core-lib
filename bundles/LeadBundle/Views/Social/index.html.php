<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<?php if (empty($socialProfiles)): ?>
<div class="alert alert-warning col-md-6 col-md-offset-3 mt-md">
    <h4><?php echo $view['translator']->trans('autoborna.lead.socialprofiles.header'); ?></h4>
    <p><a href="javascript: void(0);" onclick="Autoborna.refreshLeadSocialProfile('', <?php echo $lead->getId(); ?>, event);"><?php echo $view['translator']->trans('autoborna.lead.socialprofiles.noresults'); ?></a></p>
</div>
<?php else: ?>
<?php $count = 0; ?>
<div class="row">
<?php foreach ($socialProfiles as $integrationName => $details): ?>
    <?php if ($count > 0 && 0 == $count % 2): echo '</div><div class="row">'; endif; ?>
    <div class="col-md-6">
        <div class="panel panel-default panel-<?php echo strtolower($integrationName); ?>">
            <div class="panel-heading pr-0">
                <h3 class="panel-title"><?php echo $view['translator']->trans('autoborna.integration.'.$integrationName); ?></h3>
                <div class="panel-toolbar text-right">
                    <a href="javascript:void(0);" class="btn" data-toggle="tooltip" onclick="Autoborna.refreshLeadSocialProfile('<?php echo $integrationName; ?>', '<?php echo $lead->getId(); ?>', event);" title="<?php echo $view['translator']->trans('autoborna.lead.lead.social.lastupdate', ['%datetime%' => $view['date']->toFullConcat($details['lastRefresh'], 'utc')]); ?>">
                        <i class="text-white fa fa-refresh"></i>
                    </a>
                    <!--<a href="javascript:void(0);" class="btn" data-toggle="panelcollapse"><i class="text-white fa fa-angle-up"></i></a>-->
                    <a href="javascript:void(0);" class="btn" onclick="Autoborna.clearLeadSocialProfile('<?php echo $integrationName; ?>', '<?php echo $lead->getId(); ?>', event);" data-toggle="tooltip" title="<?php echo $view['translator']->trans('autoborna.lead.lead.social.removecache'); ?>">
                        <i class="text-white fa fa-times"></i>
                    </a>
                    <!-- trickery to allow tooltip and onclick for close button -->
                    <a class="hide <?php echo $integrationName.'-panelremove'; ?>" data-toggle="panelremove" data-parent=".col-md-6">&amp;</a>
                </div>
            </div>
             <div class="panel-collapse pull out" id="<?php echo "{$integrationName}CompleteProfile"; ?>">
                <?php echo $view->render($details['social_profile_template'], [
                'lead'              => $lead,
                'details'           => $details,
                'integrationName'   => $integrationName,
                'socialProfileUrls' => $socialProfileUrls,
            ]); ?>
            </div>
        </div>
    </div>
    <?php ++$count; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php
$view['slots']->append('modal', $view->render('AutobornaCoreBundle:Helper:modal.html.php', [
    'id'      => 'socialImageModal',
    'body'    => '<img class="img-responsive img-thumbnail" />',
    'header'  => false,
    'padding' => 'np',
]));
