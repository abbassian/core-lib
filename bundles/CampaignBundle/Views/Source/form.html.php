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

<div class="bundle-form">
    <div class="bundle-form-header mb-15">
        <h3><?php echo $view['translator']->trans('autoborna.campaign.leadsource.header.singular'); ?></h3>
        <h6 class="text-muted"><?php echo $view['translator']->trans('autoborna.campaign.leadsource.'.$sourceType.'.tooltip'); ?></h6>
    </div>

    <?php echo $view['form']->start($form); ?>


    <?php echo $view['form']->end($form); ?>
</div>