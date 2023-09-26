<?php

use Symfony\Component\Routing\RouteCollection;

//loads Autoborna's custom routing in src/Autoborna/BaseBundle/Routing/AutobornaLoader.php which
//loads all of the Autoborna bundles' routing.php files
$collection = new RouteCollection();
$collection->addCollection($loader->import('.', 'autoborna'));

return $collection;
