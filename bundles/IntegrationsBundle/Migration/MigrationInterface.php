<?php

declare(strict_types=1);

namespace Autoborna\IntegrationsBundle\Migration;

interface MigrationInterface
{
    /**
     * Returns true if the migration should be executed.
     */
    public function shouldExecute(): bool;

    /**
     * Execute migration if applicable.
     */
    public function execute(): void;
}
