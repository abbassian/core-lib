<?php

/*
 * @copyright   2015 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
?>
<html>
    <head>
        <script type="text/javascript">
            parent.postMessage("<?php echo $view->escape($response, 'js'); ?>", '*');
        </script>

        <?php echo $view['analytics']->getCode(); ?>
    </head>

    <body></body>
</html>