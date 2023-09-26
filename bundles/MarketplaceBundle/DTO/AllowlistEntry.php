<?php

declare(strict_types=1);

namespace Autoborna\MarketplaceBundle\DTO;

final class AllowlistEntry
{
    /**
     * Packagist package in the format vendor/package.
     */
    public string $package;

    /**
     * Human readable name.
     */
    public string $displayName;

    /**
     * Minimum Autoborna version in semver format (e.g. 4.1.2).
     */
    public ?string $minimumAutobornaVersion;

    /**
     * Maximum Autoborna version in semver format (e.g. 4.1.2).
     */
    public ?string $maximumAutobornaVersion;

    public function __construct(string $package, string $displayName, ?string $minimumAutobornaVersion, ?string $maximumAutobornaVersion)
    {
        $this->package              = $package;
        $this->displayName          = $displayName;
        $this->minimumAutobornaVersion = $minimumAutobornaVersion;
        $this->maximumAutobornaVersion = $maximumAutobornaVersion;
    }

    /**
     * @param array<string,mixed> $array
     */
    public static function fromArray(array $array): AllowlistEntry
    {
        return new self(
            $array['package'],
            $array['display_name'] ?? '',
            $array['minimum_autoborna_version'],
            $array['maximum_autoborna_version']
        );
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        return [
            'package'                => $this->package,
            'display_name'           => $this->displayName,
            'minimum_autoborna_version' => $this->minimumAutobornaVersion,
            'maximum_autoborna_version' => $this->maximumAutobornaVersion,
        ];
    }
}
