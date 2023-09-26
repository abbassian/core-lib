# Autoborna Integrations

> Integrations solutions structured to mirror current Integrations and created as transition to final product.

## Install integrations bundle

Bundle is to be installed as any other common plugin even it is to be a part of Autoborna in the future.

Create app/bundles/PluginBundle/Integration/UnifiedIntegrationInterface.php

```php
<?php

/*
 * @copyright   2018 Autoborna Contributors. All rights reserved
 * @author      Autoborna
 *
 * @link        http://autoborna.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Autoborna\PluginBundle\Integration;

/**
 * Interface UnifiedIntegrationInterface is used for type hinting.
 */
interface UnifiedIntegrationInterface
{
}
```

### Composer requirements and dependencies

### Sync command

`$ bin/console autoborna:integrations:sync Magento --first-time-sync --start-datetime="2019-09-12T12:00:00"`

This is how you should use it when you configure an integration (Magento in this case) and run the sync for the first time. Specify also from what date it should look for the entities to sync. This way you can controll how big batch of records you will sync with one command. If you want to sync with multiple chunks by date ranges, `--end-datetime` option will be helpful too.

The sync command in basic use looks like this:

`$ bin/console autoborna:integrations:sync Magento`

It will sync all new records from and to Autoborna for Magento. There is no need to specify the date range as Autoborna is smart enough to read the start date from the records it has already synchronized. And the end date is "now".

`$ bin/console autoborna:integrations:sync Magento --disable-pull --autoborna-object-id=contact:12 --autoborna-object-id=contact:13`

There is also option to force sync of specific objects. With the `--disable-pull` flag the sync will skip the pull process. If some `--autoborna-object-id` options are set it will not sync by a date range but rather only the IDs you will specify. `--disable-push` only disables the push. Pulling specific records by ID is not implemented yet.

The format of the `--autoborna-object-id` values is `object type[colon]object ID`. Autoborna can sync 2 object types: `contact` and `company`. The latter is not implemented yet.

The `--integration-object-id` uses the same format as `--autoborna-object-id` but it's up to each integration to support it.

Similarly, you can push specific Autoborna contacts to the integration you are developing like the following example. It can be useful if you want to push as a campaign/form/point action.

```php
$autobornaObjectIds = new \Autoborna\IntegrationsBundle\Sync\DAO\Sync\ObjectIdsDAO();
$autobornaObjectIds->addObjectId('contact', '12');
$autobornaObjectIds->addObjectId('contact', '13');

$inputOptions = new Autoborna\IntegrationsBundle\Sync\DAO\Sync\InputOptionsDAO(
    [
        'integration'      => 'Magento',
        'disable-pull'     => true,
        'autoborna-object-id' => $autobornaObjectIds,
    ]
);

/** @var \Autoborna\IntegrationsBundle\Sync\SyncService\SyncServiceInterface $syncService **/
$syncService->processIntegrationSync($inputOptions);
```

## Tests

This plugin is covered with some unit tests, functional tests, static analysis and code style check that run also in CI on every push.

### Useful commands

Always run following commands from the `plugins/IntegrationsBundle` directory.

#### `$ composer test`

With this command you can run all the tests for this plugin. Functional tests included.

#### `$ composer quicktest`

With this command you can run all the tests for this plugin except functional tests which makes it fast.

#### `$ composer phpunit -- --filter x`

With this command you can filter which tests you want to run. Replace `x` with whatever test class name or method you focus on.

#### `$ composer fixcs`

If you wan to automatically fix code styles then run this.