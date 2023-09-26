<?php

/*
 * @copyright   2019 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
/** @var \Autoborna\SmsBundle\Entity\Sms $item */
$type = $item->getSmsType();
?>

<td class="visible-sm visible-md visible-lg col-stats" data-stats="<?php echo $item->getId(); ?>">

    <?php if ('list' == $type): ?>
        <span class="mt-xs label label-default has-click-event clickable-stat<?php echo $item->getPendingCount() > 0 && 'list' === $item->getSmsType() ? '' : ' hide'; ?>""
              id="pending-<?php echo $item->getId(); ?>"
              data-toggle="tooltip"
              title="<?php echo $view['translator']->trans('autoborna.channel.stat.leadcount.tooltip'); ?>">
                            <a href="<?php echo $view['router']->path(
                                'autoborna_contact_index',
                                [
                                    'search' => $view['translator']->trans(
                                            'autoborna.lead.lead.searchcommand.sms_pending'
                                        ).':'.$item->getId(),
                                ]
                            ); ?>"><?php echo $view['translator']->trans(
                                    'autoborna.sms.stat.leadcount',
                                    ['%count%' => $item->getPendingCount()]
                                ); ?></a>
                        </span>
    <?php endif; ?>

    <span class="mt-xs label label-warning has-click-event clickable-stat"
          id="sent-count-<?php echo $item->getId(); ?>"
          data-toggle="tooltip"
          title="<?php echo $view['translator']->trans('autoborna.channel.stat.leadcount.tooltip'); ?>">
                            <a href="<?php echo $view['router']->path(
                                'autoborna_contact_index',
                                [
                                    'search' => $view['translator']->trans(
                                            'autoborna.lead.lead.searchcommand.sms_sent'
                                        ).':'.$item->getId(),
                                ]
                            ); ?>"><?php echo $view['translator']->trans(
                                    'autoborna.sms.stat.sentcount',
                                    ['%count%' => $item->getSentCount(true)]
                                ); ?></a>
                        </span>
</td>
