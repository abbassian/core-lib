<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$containerClass    = (!isset($containerClass)) ? '' : " $containerClass";
$containerAttr     = (!isset($containerAttr)) ? '' : " $containerAttr";
$size              = (!isset($size)) ? '' : ' modal-'.$size;
$class             = (!empty($class)) ? " $class" : '';
$body              = (!isset($body)) ? '' : $body;
$footer            = (!isset($footer)) ? '' : $footer;
$hidePlaceholder   = (empty($body)) ? '' : ' hide';
$header            = (!isset($header)) ? '' : $header;
$padding           = (!isset($padding)) ? '' : $padding;
$footerButtonClass = (!isset($footerButtonClass)) ? 'modal-form-buttons' : $footerButtonClass;
$dismissible       = (!isset($dismissible)) ? true : $dismissible;
?>

<div class="modal fade<?php echo $containerClass; ?>" id="<?php echo $id; ?>" data-backdrop="static" role="dialog" aria-labelledby="<?php echo $id; ?>-label" aria-hidden="true"<?php echo $containerAttr; ?>>
    <div class="modal-dialog<?php echo $size; ?>">
        <div class="modal-content<?php echo $class; ?>">
            <?php if (false !== $header): ?>
            <div class="modal-header">
                <?php if ($dismissible): ?>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span aria-hidden="true">&times;</span></button>
                <?php endif; ?>

                <h4 class="modal-title" id="<?php echo $id; ?>-label">
                    <?php echo $header; ?>
                </h4>

                <!-- start: loading bar -->
                <div class="modal-loading-bar">
                    <?php echo $view['translator']->trans('autoborna.core.loading'); ?>
                </div>
                <!--/ end: loading bar -->

            </div>
            <?php endif; ?>
            <div class="modal-body <?php echo $padding; ?>">
                <div class="loading-placeholder<?php echo $hidePlaceholder; ?>">
                    <?php echo $view['translator']->trans('autoborna.core.loading'); ?>
                </div>
                <div class="modal-body-content">
                    <?php echo $body; ?>
                </div>
            </div>
            <?php if (!empty($footer) || !empty($footerButtons)) : ?>
            <div class="modal-footer">
                <?php if (!empty($footerButtons)): ?>
                <div class="<?php echo $footerButtonClass; ?>">
                    <?php if (is_array($footerButtons)): ?>
                    <?php foreach ($footerButtons as $button): ?>
                        <button type="button"
                                class="btn <?php echo !empty($button['class']) ? $button['class'] : 'btn-default'; ?>"
                                <?php if (!empty($button['attr'])): echo ' '.$button['attr']; endif; ?>>
                            <?php if (!empty($button['textIcon'])): ?><i class="<?php echo $button['textIcon']; ?>"></i><?php endif; ?>
                            <?php echo $button['label']; ?>
                        </button>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <?php echo $footer; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
