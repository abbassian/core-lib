<?php

/*
 * @copyright   2016 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>

<div>
<?php echo $view->render('AutobornaAssetBundle:Asset:preview.html.php', ['activeAsset' => $event['extra']['asset'], 'assetDownloadUrl' => $view['router']->url(
    'autoborna_asset_action',
    ['objectAction' => 'preview', 'objectId' => $event['extra']['asset']->getId()]
)]); ?>
</div>