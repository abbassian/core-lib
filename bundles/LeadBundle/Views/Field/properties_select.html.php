<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$type  = $type ?? 'select';
$value = (isset($value)) ? $value : '';
if (!isset($form) || !$form->vars['value']) {
    $html = str_replace(['properties_'.$type.'_template', 'leadfield_properties'], ['properties', 'leadfield_properties_template'], $selectTemplate);
} else {
    $html = $view['form']->row($form);
}
?>

<div class="<?php echo $type; ?>">
    <?php echo $html; ?>
</div>