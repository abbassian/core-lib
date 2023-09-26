<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$autobornaContent = $view['slots']->get(
    'autobornaContent',
    isset($autobornaTemplateVars['autobornaContent']) ? $autobornaTemplateVars['autobornaContent'] : ''
);
?>

<script>
    var autobornaBasePath    = '<?php echo $app->getRequest()->getBasePath(); ?>';
    var autobornaBaseUrl     = '<?php echo $view['router']->path('autoborna_base_index'); ?>';
    var autobornaAjaxUrl     = '<?php echo $view['router']->path('autoborna_core_ajax'); ?>';
    var autobornaAjaxCsrf    = '<?php echo $view['security']->getCsrfToken('autoborna_ajax_post'); ?>';
    var autobornaImagesPath  = '<?php echo $view['assets']->getImagesPath(); ?>';
    var autobornaAssetPrefix = '<?php echo $view['assets']->getAssetPrefix(true); ?>';
    var autobornaContent     = '<?php echo $autobornaContent; ?>';
    var autobornaEnv         = '<?php echo $app->getEnvironment(); ?>';
    var autobornaLang        = <?php echo $view['translator']->getJsLang(); ?>;
    var autobornaLocale      = '<?php echo $app->getRequest()->getLocale(); ?>';
    var autobornaEditorFonts = <?php echo json_encode($view['config']->get('editor_fonts')); ?>;
</script>
<?php $view['assets']->outputSystemScripts(true); ?>
