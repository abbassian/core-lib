<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$view->extend('AutobornaCoreBundle:Default:content.html.php');
$header = ($activeAsset->getId()) ?
    $view['translator']->trans('autoborna.asset.asset.menu.edit',
        ['%name%' => $activeAsset->getTitle()]) :
    $view['translator']->trans('autoborna.asset.asset.menu.new');
$view['slots']->set('headerTitle', $header);
$view['slots']->set('autobornaContent', 'asset');
?>
<script>
	<?php echo 'autobornaAssetUploadEndpoint = "'.$uploadEndpoint.'";'; ?>
	<?php echo 'autobornaAssetUploadMaxSize = '.$maxSize.';'; ?>
	<?php echo 'autobornaAssetUploadMaxSizeError = "'.$maxSizeError.'";'; ?>
	<?php echo 'autobornaAssetUploadExtensions = "'.$extensions.'";'; ?>
	<?php echo 'autobornaAssetUploadExtensionError = "'.$extensionError.'";'; ?>
</script>
<?php echo $view['form']->start($form); ?>
<!-- start: box layout -->
<div class="box-layout">
    <!-- container -->
    <div class="col-md-9 bg-auto height-auto bdr-r">
        <div class="pa-md">
	        <div class="row">
		        <div class="col-md-6">
					<div class="col-md-7 pl-0">
						<?php echo $view['form']->row($form['storageLocation']); ?>
					</div>
			        <div class="col-md-5 text-left mt-lg<?php if ($startOnLocal) {
    echo ' hide';
} ?>" id="remote-button">
						<?php if ($integrations) : ?>
							<a data-toggle="ajaxmodal" data-target="#RemoteFileModal" data-header="<?php echo $view['translator']->trans('autoborna.asset.remote.file.browse'); ?>" href="<?php echo $view['router']->path('autoborna_asset_remote'); ?>?tmpl=modal" class="btn btn-primary">
								<?php echo $view['translator']->trans('autoborna.asset.remote.file.browse'); ?>
							</a>
						<?php endif; ?>
					</div>
			        <div id="storage-local"<?php if (!$startOnLocal) {
    echo ' class="hide"';
} ?>>
				        <div class="row">
					        <div class="form-group col-xs-12 ">
						        <?php echo $view['form']->label($form['tempName']); ?>
						        <?php echo $view['form']->widget($form['tempName']); ?>
						        <?php echo $view['form']->errors($form['tempName']); ?>
						        <div class="help-block mdropzone-error"></div>
						        <div class="mdropzone text-center" id="dropzone">
						        	<div class="dz-message">
						        		<h4><?php echo $view['translator']->trans('autoborna.asset.drop.file.here'); ?></h4>
									</div>
						        </div>
					        </div>
				        </div>
			        </div>
			        <div id="storage-remote"<?php if ($startOnLocal) {
    echo ' class="hide"';
} ?>>
				        <?php echo $view['form']->row($form['remotePath']); ?>
			        </div>
		    	</div>
		    	<div class="col-md-6">
		    		<div class="row">
				    	<div class="form-group col-xs-12 preview">
				    		<?php echo $view->render('AutobornaAssetBundle:Asset:preview.html.php', ['activeAsset' => $activeAsset, 'assetDownloadUrl' => $view['router']->url(
                                'autoborna_asset_action',
                                ['objectAction' => 'preview', 'objectId' => $activeAsset->getId()]
                            )]); ?>
			    		</div>
		    		</div>
		    	</div>
		    </div>
		    <div class="row">
				<div class="col-md-6">
					<?php echo $view['form']->row($form['title']); ?>
				</div>
				<div class="col-md-6">
					<?php echo $view['form']->row($form['alias']); ?>
				</div>
			</div>
            <div class="row">
                <div class="col-xs-12">
                    <?php echo $view['form']->row($form['description']); ?>
                </div>
            </div>
		</div>
	</div>
 	<div class="col-md-3 bg-white height-auto">
		<div class="pr-lg pl-lg pt-md pb-md">
			<?php
                echo $view['form']->row($form['category']);
                echo $view['form']->row($form['language']);
                echo $view['form']->row($form['isPublished']);
                echo $view['form']->row($form['publishUp']);
                echo $view['form']->row($form['publishDown']);
                echo $view['form']->row($form['disallow']);
            ?>
		</div>
	</div>
</div>
<?php echo $view['form']->end($form); ?>

<?php if ($integrations) : ?>
	<?php echo $view->render('AutobornaCoreBundle:Helper:modal.html.php', [
        'id'            => 'RemoteFileModal',
        'size'          => 'lg',
        'footerButtons' => true,
    ]); ?>
<?php endif; ?>
