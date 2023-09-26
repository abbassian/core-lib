<?php

$container->setParameter('kernel.logs_dir', '%kernel.root_dir%/../../var/logs');
$container->setParameter('autoborna.cache_path', '%kernel.root_dir%/../../var/cache');
$container->setParameter('autoborna.log_path', '%kernel.root_dir%/../../var/logs');
$container->setParameter('autoborna.tmp_path', '%kernel.root_dir%/../../var/tmp');
$container->setParameter('autoborna.mailer_spool_path', '%kernel.root_dir%/../../var/tmp');
