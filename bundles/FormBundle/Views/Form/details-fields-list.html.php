<?php

/*
 * @copyright   2020 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

?>
<div class="box-layout">
    <div class="col-md-1 va-m">
        <?php $requiredTitle = $field->getIsRequired() ? 'autoborna.core.required'
            : 'autoborna.core.not_required'; ?>
        <h3><span class="fa fa-<?php echo $field->getIsRequired() ? 'check'
                : 'times'; ?> text-white dark-xs" data-toggle="tooltip"
                  data-placement="left"
                  title="<?php echo $view['translator']->trans($requiredTitle); ?>"></span>
        </h3>
    </div>
    <div class="col-md-7 va-m">
        <h5 class="fw-sb text-primary mb-xs"><?php echo $field->getLabel(); ?></h5>
        <h6 class="text-white dark-md"><?php echo $view['translator']->trans(
                'autoborna.form.details.field_type',
                ['%type%' => $field->getType()]
            ); ?></h6>
    </div>
    <div class="col-md-4 va-m text-right">
        <?php if (!$field->getParent()): ?>
            <em class="text-white dark-sm">
                <?php echo $view['translator']->trans(
                    'autoborna.form.details.field_order',
                    ['%order%' => $field->getOrder()]
                ); ?></em>
        <?php endif; ?>
    </div>
</div>
