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
<?php echo $view['content']->getCustomContent('details.top', $autobornaTemplateVars); ?>
<?php if (method_exists($entity, 'getCategory')): ?>
<tr>
    <td width="20%"><span class="fw-b textTitle"><?php echo $view['translator']->trans('autoborna.core.category'); ?></span></td>
    <td><?php echo is_object($entity->getCategory()) ? $entity->getCategory()->getTitle() : $view['translator']->trans('autoborna.core.form.uncategorized'); ?></td>
</tr>
<?php endif; ?>

<?php if (method_exists($entity, 'getCreatedByUser')): ?>
<tr>
    <td width="20%"><span class="fw-b textTitle"><?php echo $view['translator']->trans('autoborna.core.createdby'); ?></span></td>
    <td><?php echo $entity->getCreatedByUser(); ?></td>
</tr>
<tr>
    <td width="20%"><span class="fw-b textTitle"><?php echo $view['translator']->trans('autoborna.core.created'); ?></span></td>
    <td><?php echo $view['date']->toFull($entity->getDateAdded()); ?></td>
</tr>
<?php endif; ?>
<?php
if (method_exists($entity, 'getModifiedByUser')):
$modified = $entity->getModifiedByUser();
if ($modified):
    ?>
    <tr>
        <td width="20%"><span class="fw-b textTitle"><?php echo $view['translator']->trans('autoborna.core.modifiedby'); ?></span></td>
        <td><?php echo $entity->getModifiedByUser(); ?></td>
    </tr>
    <tr>
        <td width="20%"><span class="fw-b textTitle"><?php echo $view['translator']->trans('autoborna.core.modified'); ?></span></td>
        <td><?php echo $view['date']->toFull($entity->getDateModified()); ?></td>
    </tr>
<?php endif; ?>
<?php endif; ?>
<?php if (method_exists($entity, 'getPublishUp')): ?>
<tr>
    <td width="20%"><span class="fw-b textTitle"><?php echo $view['translator']->trans('autoborna.page.publish.up'); ?></span></td>
    <td><?php echo (!is_null($entity->getPublishUp())) ? $view['date']->toFull($entity->getPublishUp()) : $view['date']->toFull($entity->getDateAdded()); ?></td>
</tr>
<tr>
    <td width="20%"><span class="fw-b textTitle"><?php echo $view['translator']->trans('autoborna.page.publish.down'); ?></span></td>
    <td><?php echo (!is_null($entity->getPublishDown())) ? $view['date']->toFull($entity->getPublishDown()) : $view['translator']->trans('autoborna.core.never'); ?></td>
</tr>
<?php endif; ?>
<?php if (method_exists($entity, 'getId')): ?>
    <tr>
        <td width="20%"><span class="fw-b textTitle"><?php echo $view['translator']->trans('autoborna.core.id'); ?></span></td>
        <td><?php echo $entity->getId(); ?></td>
    </tr>
<?php endif; ?>
<?php echo $view['content']->getCustomContent('details.bottom', $autobornaTemplateVars); ?>