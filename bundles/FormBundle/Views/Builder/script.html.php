<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$scriptSrc = $view['assets']->getUrl('media/js/'.('dev' == $app->getEnvironment() ? 'autoborna-form-src.js' : 'autoborna-form.js'), null, null, true);
$scriptSrc = str_replace('/index_dev.php', '', $scriptSrc);
?>

<script type="text/javascript">
    /** This section is only needed once per page if manually copying **/
    if (typeof AutobornaSDKLoaded == 'undefined') {
        var AutobornaSDKLoaded = true;
        var head            = document.getElementsByTagName('head')[0];
        var script          = document.createElement('script');
        script.type         = 'text/javascript';
        script.src          = '<?php echo $scriptSrc; ?>';
        script.onload       = function() {
            AutobornaSDK.onLoad();
        };
        head.appendChild(script);
        var AutobornaDomain = '<?php echo str_replace('/index_dev.php', '', $view['assets']->getBaseUrl()); ?>';
        var AutobornaLang   = {
            'submittingMessage': "<?php echo $view['translator']->trans('autoborna.form.submission.pleasewait'); ?>"
        }
    }else if (typeof AutobornaSDK != 'undefined') {
        AutobornaSDK.onLoad();
    }
</script>
