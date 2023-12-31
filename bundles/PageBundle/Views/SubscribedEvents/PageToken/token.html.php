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
<div id="pageTokens">
    <div class="row ml-2 mr-2 mb-2">
        <div class="col-sm-6">
            <a href="#" data-toggle="tooltip" data-token="{langbar}" class="btn btn-default btn-block" title="<?php echo $view['translator']->trans('autoborna.page.token.lang.descr'); ?>">
                <i class="fa fa-language"></i><br />
                <?php echo $view['translator']->trans('autoborna.page.token.lang'); ?>
            </a>
        </div>
        <div class="col-sm-6">
            <a href="#" data-toggle="tooltip" data-token="{sharebuttons}" class="btn btn-default btn-block" title="<?php echo $view['translator']->trans('autoborna.page.token.share.descr'); ?>">
                <i class="fa fa-share-alt-square"></i><br />
                <?php echo $view['translator']->trans('autoborna.page.token.share'); ?>
            </a>
        </div>
    </div>
</div>