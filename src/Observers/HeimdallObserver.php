<?php

declare(strict_types=1);

namespace AMgrade\Heimdall\Observers;

use AMgrade\Heimdall\Services\HeimdallService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

use function is_string;

use const null;
use const true;

class HeimdallObserver
{
    protected array $config;

    protected HeimdallService $service;

    public function __construct(HeimdallService $service)
    {
        $this->config = Config::get('heimdall');
        $this->service = $service;
    }

    public function handle(Model $model): bool
    {
        $key = $this->config['observer']['models'][$model::class] ?? null;

        if (!is_string($key) || '' === $key) {
            return true;
        }

        $attribute = $model->getAttribute($key);

        if (null === $attribute || '' === $attribute) {
            return true;
        }

        return !$this->service->allMatch($attribute);
    }
}
