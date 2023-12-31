<?php

/*
 * @copyright   2014 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
$fields    = $form->children;
$fieldKeys = array_keys($fields);
$template  = '<div class="col-md-6">{content}</div>';
?>

<?php if (count(array_intersect($fieldKeys, ['site_url', 'webroot', '404_page', 'cache_path', 'log_path', 'theme', 'image_path']))): ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('autoborna.core.config.header.general'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'site_url', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'webroot', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, '404_page', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'cache_path', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'log_path', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'theme', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'image_path', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'last_shown_tab'); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (count(array_intersect($fieldKeys, ['default_pagelist', 'timezone', 'locale', 'date_format_full', 'date_format_short', 'date_format_dateonly', 'date_format_timeonly']))): ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('autoborna.core.config.header.defaults'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'default_pagelimit', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'default_timezone', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'locale', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'cached_data_timeout', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'default_daterange_filter', $template); ?>
        </div>

        <hr class="text-muted" />

        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'date_format_full', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'date_format_short', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'date_format_dateonly', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'date_format_timeonly', $template); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (count(array_intersect($fieldKeys, ['cors_restrict_domains']))): ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('autoborna.core.config.header.cors'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'cors_restrict_domains', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'cors_valid_domains', $template); ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (count(array_intersect($fieldKeys, ['trusted_hosts', 'trusted_proxies', 'ip_lookup_service', 'do_not_track_ips', 'do_not_track_bots', 'transliterate_page_title']))): ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('autoborna.core.config.header.misc'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'trusted_hosts', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'trusted_proxies', $template); ?>
        </div>

        <?php if (isset($fields['trusted_hosts'])): ?>
        <hr class="text-muted" />
        <?php endif; ?>

        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'ip_lookup_service', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'ip_lookup_auth', $template); ?>
            <?php echo $view['form']->rowIfExists($fields, 'ip_lookup_create_organization', $template); ?>
            <div id="ip_lookup_config_container">
            <?php echo $view['form']->rowIfExists($fields, 'ip_lookup_config', '<div class="col-md-12">{content}</div>'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="small text-center" id="ip_lookup_attribution"><?php echo $ipLookupAttribution; ?></div>
            </div>
        </div>

        <?php if (isset($fields['do_not_track_ips']) || isset($fields['do_not_track_bots'])): ?>
        <hr class="text-muted" />
        <div class="row">
            <?php if (isset($fields['do_not_track_ips'])): ?>
                    <?php echo $view['form']->rowIfExists($fields, 'do_not_track_ips', $template); ?>
            <?php endif; ?>
            <?php if (isset($fields['do_not_track_bots'])): ?>
                    <?php echo $view['form']->rowIfExists($fields, 'do_not_track_bots', $template); ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($fields['link_shortener_url'])): ?>
        <hr class="text-muted" />
        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'link_shortener_url', $template); ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($fields['max_entity_lock_time'])): ?>
        <hr class="text-muted" />
        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'max_entity_lock_time', $template); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($fields['transliterate_page_title'])): ?>
        <hr class="text-muted" />
        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'transliterate_page_title', $template); ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if (count(array_intersect($fieldKeys, ['update_stability', 'composer_updates']))): ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $view['translator']->trans('autoborna.core.config.header.update'); ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php echo $view['form']->rowIfExists($fields, 'update_stability', $template); ?>
            <?php echo $view['form']->rowIfExists(
                $fields,
                'composer_updates',
                '<div class="col-md-6">{content}<span style="color:red">'.$view['translator']->trans(
                    'autoborna.core.config.form.update.composer.warning',
                    [
                        '%url_start%' => '<a href="https://mau.tc/switch-to-composer" target="_blank" style="color: red; text-decoration: underline">',
                        '%url_end%'   => '</a>',
                    ]).'</div>'
            ); ?>
        </div>
    </div>
</div>
<?php endif; ?>
