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

<li class="dropdown dropdown-custom" id="globalSearchDropdown">
    <div class="dropdown-menu">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">
                    <h6 class="fw-sb"><?php echo $view['translator']->trans('autoborna.core.search.results'); ?></h6>
                </div>
            </div>
            <div class="pt-0 pb-xs pl-0 pr-0">
                <div class="scroll-content slimscroll" style="height:250px;" id="globalSearchResults">
                    <?php echo $view->render('AutobornaCoreBundle:GlobalSearch:results.html.php', [
                        'results' => $results,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</li>
<li>
    <div class="search-container" id="globalSearchContainer">
        <a href="javascript: void(0);" class="search-button">
            <i class="fa fa-search fs-16"></i>
        </a>
        <input type="search" value="<?php echo $view->escape($searchString); ?>" class="form-control search" id="globalSearchInput" name="global_search" placeholder="<?php echo $view['translator']->trans('autoborna.core.search.everything.placeholder'); ?>" value="" autocomplete="false" data-toggle="livesearch" data-target="#globalSearchResults" data-action="<?php echo $view['router']->path('autoborna_core_ajax', ['action' => 'globalSearch']); ?>" data-overlay="true" data-overlay-text="<?php echo $view['translator']->trans('autoborna.core.search.livesearch'); ?>" />
    </div>
</li>
