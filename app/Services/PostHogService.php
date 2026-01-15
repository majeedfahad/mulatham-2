<?php

namespace App\Services;

use PostHog\PostHog;

class PostHogService
{
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = config('posthog.enabled') && config('posthog.api_key');

        if ($this->enabled) {
            PostHog::init(
                config('posthog.api_key'),
                ['host' => config('posthog.host')]
            );
        }
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function capture(string $distinctId, string $event, array $properties = []): void
    {
        if (! $this->enabled) {
            return;
        }

        PostHog::capture([
            'distinctId' => $distinctId,
            'event' => $event,
            'properties' => $properties,
        ]);
    }

    public function identify(string $distinctId, array $properties = []): void
    {
        if (! $this->enabled) {
            return;
        }

        PostHog::identify([
            'distinctId' => $distinctId,
            'properties' => $properties,
        ]);
    }

    public function alias(string $distinctId, string $alias): void
    {
        if (! $this->enabled) {
            return;
        }

        PostHog::alias([
            'distinctId' => $distinctId,
            'alias' => $alias,
        ]);
    }

    public function groupIdentify(string $groupType, string $groupKey, array $properties = []): void
    {
        if (! $this->enabled) {
            return;
        }

        PostHog::groupIdentify([
            'groupType' => $groupType,
            'groupKey' => $groupKey,
            'properties' => $properties,
        ]);
    }

    public function flush(): void
    {
        if (! $this->enabled) {
            return;
        }

        PostHog::flush();
    }
}
