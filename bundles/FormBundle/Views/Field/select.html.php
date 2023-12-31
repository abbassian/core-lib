<?php

$defaultInputFormClass = ' not-chosen';
$defaultInputClass     = 'selectbox';
$containerType         = 'select';

include __DIR__.'/field_helper.php';

if (!empty($properties['multiple'])) {
    $inputAttr .= ' multiple="multiple"';
}

$label = (!$field['showLabel']) ? '' : <<<HTML

                <label $labelAttr>{$field['label']}</label>
HTML;

$help = (empty($field['helpMessage'])) ? '' : <<<HTML

                <span class="autobornaform-helpmessage">{$field['helpMessage']}</span>
HTML;

$emptyOption = '';
if ((!empty($properties['placeholder']) || empty($field['defaultValue']) && empty($properties['multiple']))):
    $placeholder = $properties['placeholder'] ?? '';
    $emptyOption = <<<HTML
                    <option value="">{$placeholder}</option>
HTML;
endif;

$optionBuilder = function (array $list, $emptyOptionHtml = '') use (&$optionBuilder, $field, $view) {
    $html = $emptyOptionHtml;
    foreach ($list as $listValue => $listLabel):
        if (is_array($listLabel)) {
            // This is an option group
            $html .= <<<HTML

                    <optgroup label="$listValue">
                    {$optionBuilder($listLabel)}
                    </optgroup>

HTML;

            continue;
        }

    $selected  = ($listValue === $field['defaultValue']) ? ' selected="selected"' : '';
    $html .= <<<HTML
                    <option value="{$view->escape($listValue)}"{$selected}>{$view->escape($listLabel)}</option>
HTML;
    endforeach;

    return $html;
};

$optionsHtml = $optionBuilder($list, $emptyOption);
$html        = <<<HTML

            <div $containerAttr>{$label}{$help}
                <select $inputAttr>$optionsHtml
                </select>
                <span class="autobornaform-errormsg" style="display: none;">$validationMessage</span>
            </div>

HTML;

echo $html;
