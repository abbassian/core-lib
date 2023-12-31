<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
if (!isset($class)) {
    $class = 'table';
}
if (!isset($shortenLinkText)) {
    $shortenLinkText = 30;
}
$showHeaders = [
    'autoborna.dashboard.label.url',
    'autoborna.dashboard.label.unique.hit.count',
    'autoborna.dashboard.label.total.hit.count',
    'autoborna.dashboard.label.email.name',
];
$showValues = [
    'url',
    'unique_hits',
    'hits',
    'email_name',
];
?>

<table class="<?php echo $class; ?>">
    <?php if (!empty($headItems)) : ?>
        <thead>
            <tr>
            <?php foreach ($headItems as $headItem) : ?>
                <?php if (in_array($headItem, $showHeaders)) : ?>
                <th><?php echo $view['translator']->trans($headItem); ?></th>
                <?php endif; ?>
            <?php endforeach; ?>
            </tr>
        </thead>
    <?php endif; ?>
    <?php if (!empty($bodyItems)) : ?>
        <tbody>
            <?php foreach ($bodyItems as $id => $row) : ?>
                <tr>
                    <?php if (is_array($row)) : ?>
                        <?php foreach ($row as $key => $item) : ?>
                            <?php if (in_array($key, $showValues)) : ?>
                            <td>
                                <?php if ('email_name' === $key) : ?>
                                    <a href="<?php echo $view['router']->path('autoborna_email_action', ['objectAction' => 'view', 'objectId' => $row['email_id']]); ?>"
                                        title="<?php echo $item; ?>"
                                        data-toggle="ajax">
                                        <?php echo $view['assets']->shortenText($item, $shortenLinkText); ?>
                                    </a>
                                <?php else: ?>
                                    <?php echo $item; ?>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    <?php endif; ?>
</table>
