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
$view['slots']->set('autobornaContent', 'point');

$header = ($entity->getId()) ?
    $view['translator']->trans('autoborna.point.menu.edit',
        ['%name%' => $view['translator']->trans($entity->getName())]) :
    $view['translator']->trans('autoborna.point.menu.new');
$view['slots']->set('headerTitle', $header);

echo $view['form']->start($form);
?>
<!-- start: box layout -->
<div class="box-layout">
    <!-- container -->
    <div class="col-md-9 bg-auto height-auto bdr-r">
    	<div class="row">
    		<div class="col-md-6">
		        <div class="pa-md">
				    <?php
                    echo $view['form']->row($form['name']);
                    echo $view['form']->row($form['description']);
                    ?>
				</div>
			</div>
			<div class="col-md-6">
				<div class="pa-md">
                    <?php echo $view['form']->row($form['delta']); ?>
					<?php echo $view['form']->row($form['type']); ?>
					<div id="pointActionProperties">
                        <?php
                        if (isset($form['properties'])):
                            echo $view['form']->row($form['properties']);
                        endif;
                        ?>
					</div>
				</div>
			</div>
		</div>
	</div>
 	<div class="col-md-3 bg-white height-auto">
		<div class="pr-lg pl-lg pt-md pb-md">
			<?php
                echo $view['form']->row($form['category']);
                echo $view['form']->row($form['isPublished']);
                echo $view['form']->row($form['repeatable']);
                echo $view['form']->row($form['publishUp']);
                echo $view['form']->row($form['publishDown']);
            ?>
		</div>
	</div>
</div>
<?php echo $view['form']->end($form); ?>