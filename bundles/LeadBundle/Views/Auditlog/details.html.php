<?php

$details = $event['details'];
$type    = $event['eventType'];
$text    = '';
$objects = isset($details['fields']) ? $details['fields'] : [];
unset($objects['dateModified']);
foreach ($details as $key => $value) {
    if (!array_key_exists($key, $objects) && 'fields' !== $key && 'dateIdentified' !== $key && 'dateModified' !== $key) {
        $objects[$key] = $value;
    }
}
if (0 === count($objects)) {
    return '';
}
switch ($type) {
    case 'create':
        $text = '<table class="table">';
        $text .= '<tr>';
        $text .= '<th>Field/Object</th><th>New Value</th><th>Old Value</th>';
        $text .= '</tr>';
        foreach ($objects as $field => $values) {
            $text .= '<tr>';
            if (is_array($values)) {
                if (count($values) >= 2) {
                    $text .= "<td>{$view->escape($field)}</td><td>{$view->escape($view['formatter']->normalizeStringValue($values[1]))}</td><td>{$view->escape($view['formatter']->normalizeStringValue($values[0]))}</td>";
                } else {
                    $v = '';
                    foreach ($values as $k => $value) {
                        $v = $k.': '.$view->escape(implode(', ', $value));
                    }
                    $text .= "<td>{$view->escape($field)}</td><td>{$view->escape($v)}</td><td>&nbsp;</td>";
                }
            } else {
                $text .= "<td>{$view->escape($field)}</td><td>{$view->escape($view['formatter']->normalizeStringValue($values))}</td><td>&nbsp;</td>";
            }
            $text .= '</tr>';
        }
        $text .= '</table>';
        break;
    case 'delete':
        $text = $view['translator']->trans('autoborna.lead.audit.deleted');
        break;
    case 'update':
        $text = '<table class="table">';
        $text .= '<tr>';
        $text .= '<th>Field/Object</th><th>New Value</th><th>Old Value</th>';
        $text .= '</tr>';
        foreach ($objects as $field => $values) {
            $text .= '<tr>';
            if (is_array($values)) {
                if (count($values) >= 2) {
                    $text .= "<td>{$view->escape($field)}</td><td>{$view->escape($view['formatter']->normalizeStringValue($values[1]))}</td><td>{$view->escape($view['formatter']->normalizeStringValue($values[0]))}</td>";
                } else {
                    $v = '';
                    foreach ($values as $k => $value) {
                        $v = $k.': '.$view->escape(implode(', ', $value));
                    }
                    $text .= "<td>{$view->escape($field)}</td><td>$v</td><td>&nbsp;</td>";
                }
            } else {
                $text .= "<td>{$view->escape($field)}</td><td>{$view->escape($view['formatter']->normalizeStringValue($values))}</td><td>&nbsp;</td>";
            }
            $text .= '</tr>';
        }
        $text .= '</table>';
        break;
    case 'identified':
        $text = $view['translator']->trans('autoborna.lead.audit.identified');
        break;
    case 'ipadded':
        $text = $view['translator']->trans('autoborna.lead.audit.accessed').' '.implode(',', array_splice($details, 1));
        break;
    case 'merged':
        $text = $view['translator']->trans('autoborna.lead.audit.merged');
        break;
}
echo $text;
echo '<!-- '.PHP_EOL.json_encode($details, JSON_PRETTY_PRINT).PHP_EOL.' -->';
