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
<!DOCTYPE html>
<html>
    <?php echo $view->render('AutobornaCoreBundle:Default:head.html.php'); ?>
    <body class="header-fixed">
        <!-- start: app-wrapper -->
        <section id="app-wrapper">
            <?php $view['assets']->outputScripts('bodyOpen'); ?>

            <!-- start: app-sidebar(left) -->
            <aside class="app-sidebar sidebar-left">
                <?php echo $view->render('AutobornaCoreBundle:LeftPanel:index.html.php'); ?>
            </aside>
            <!--/ end: app-sidebar(left) -->

            <!-- start: app-sidebar(right) -->
            <aside class="app-sidebar sidebar-right">
                <?php echo $view->render('AutobornaCoreBundle:RightPanel:index.html.php'); ?>
            </aside>
            <!--/ end: app-sidebar(right) -->

            <!-- start: app-header -->
            <header id="app-header" class="navbar">
               <?php echo $view->render('AutobornaCoreBundle:Default:navbar.html.php'); ?>

               <?php echo $view->render('AutobornaCoreBundle:Notification:flashes.html.php'); ?>
            </header>
            <!--/ end: app-header -->

            <!-- start: app-footer(need to put on top of #app-content)-->
            <footer id="app-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-6 text-muted"><?php echo $view['translator']->trans('autoborna.core.copyright', ['%date%' => date('Y')]); ?></div>
                        <div class="col-xs-6 text-muted text-right small">v<?php
                            /** @var \Autoborna\CoreBundle\Templating\Helper\VersionHelper $version */
                            $version = $view['version'];
                            echo $version->getVersion(); ?>
                        </div>
                    </div>
                </div>
            </footer>
            <!--/ end: app-footer -->

            <!-- start: app-content -->
            <section id="app-content">
                <?php $view['slots']->output('_content'); ?>
            </section>
            <!--/ end: app-content -->

        </section>
        <!--/ end: app-wrapper -->

        <script>
            Autoborna.onPageLoad('body');
            <?php if ('dev' === $app->getEnvironment()): ?>
            mQuery( document ).ajaxComplete(function(event, XMLHttpRequest, ajaxOption){
                if(XMLHttpRequest.responseJSON && typeof XMLHttpRequest.responseJSON.ignore_wdt == 'undefined' && XMLHttpRequest.getResponseHeader('x-debug-token')) {
                    if (mQuery('[class*="sf-tool"]').length) {
                        mQuery('[class*="sf-tool"]').remove();
                    }

                    mQuery.get(autobornaBaseUrl + '_wdt/'+XMLHttpRequest.getResponseHeader('x-debug-token'),function(data){
                        mQuery('body').append('<div class="sf-toolbar-reload">'+data+'</div>');
                    });
                }
            });
            <?php endif; ?>
        </script>
        <?php $view['assets']->outputScripts('bodyClose'); ?>
        <?php echo $view->render('AutobornaCoreBundle:Helper:modal.html.php', [
            'id'            => 'AutobornaSharedModal',
            'footerButtons' => true,
        ]); ?>
    </body>
</html>
