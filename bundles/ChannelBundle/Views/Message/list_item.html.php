<?php

/*
 * @copyright   2016 Autoborna Contributors. All rights reserved
 * @author      Autoborna, Inc.
 *
 * @link        https://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

/** @var \Autoborna\ChannelBundle\Entity\Message $item */
$messageChannels = $item->getChannels();
$channels        = [];
if ($messageChannels) {
    foreach ($messageChannels as $channelName => $channel) {
        if (!$channel->isEnabled()) {
            continue;
        }

        $channels[] = $view['translator']->hasId('autoborna.channel.'.$channelName)
            ? $view['translator']->trans('autoborna.channel.'.$channelName)
            : ucfirst(
                $channelName
            );
    }
}
?>

<td class="visible-md visible-lg">
    <?php foreach ($channels as $channel): ?>
    <span class="label label-default"><?php echo $channel; ?></span>
    <?php endforeach; ?>
</td>