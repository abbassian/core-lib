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

<?php echo $view['form']->start($form); ?>
    <div class="box-layout">
        <ul class="nav nav-tabs pr-md pl-md mt-10">
            <?php $step = 1; ?>
            <?php foreach ($groups as $g): ?>
                <?php if (!empty($fields[$g])): ?>
                    <li class="<?php if (1 === $step) {
    echo 'active';
} ?>">
                        <a href="#company-<?php echo $g; ?>" class="steps" data-toggle="tab">
                            <?php echo $view['translator']->trans('autoborna.lead.field.group.'.$g); ?>
                        </a>
                    </li>
                    <?php ++$step; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="tab-content pa-md">
        <?php echo $view->render(
            'AutobornaLeadBundle:Company:form_fields.html.php',
            ['form' => $form, 'groups' => $groups, 'fields' => $fields, 'embedded' => true]
        ); ?>
    </div>
    </div>

<?php echo $view['form']->end($form); ?>