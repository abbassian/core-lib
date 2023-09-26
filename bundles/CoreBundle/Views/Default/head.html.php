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
<head>
    <meta charset="UTF-8" />
    <title><?php if (!empty($view['slots']->get('headerTitle', ''))): ?>
        <?php echo strip_tags(str_replace('<', ' <', $view['slots']->get('headerTitle', ''))); ?> | 
    <?php endif; ?>
	<?php echo $view['slots']->get('pageTitle', 'Autoborna'); ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="icon" type="image/x-icon" href="<?php echo $view['assets']->getUrl('media/images/favicon.ico'); ?>" />
    <link rel="icon" sizes="192x192" href="<?php echo $view['assets']->getUrl('media/images/favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php echo $view['assets']->getUrl('media/images/apple-touch-icon.png'); ?>" />

    <?php echo $view['assets']->outputSystemStylesheets(); ?>

    <?php echo $view->render('AutobornaCoreBundle:Default:script.html.php'); ?>
    <?php $view['assets']->outputHeadDeclarations(); ?>
</head>
