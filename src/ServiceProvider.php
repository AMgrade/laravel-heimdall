<?php

declare(strict_types=1);

namespace AMgrade\Heimdall;

use AMgrade\Heimdall\Observers\HeimdallObserver;
use AMgrade\Heimdall\Validation\Rules\HeimdallRule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

use function class_exists;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/heimdall.php' => $this->app->configPath('heimdall.php'),
            ], 'config');
        }

        $this->registerObservers();

        $this->registerValidationRule();
    }

    public function register(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'validation');

        $this->mergeConfigFrom(__DIR__.'/../config/heimdall.php', 'heimdall');
    }

    protected function registerObservers(): void
    {
        $config = Config::get('heimdall.observer', []);

        foreach ($config['models'] ?? [] as $class => $attribute) {
            if (!class_exists($class)) {
                continue;
            }

            foreach ($config['events'] ?? [] as $event) {
                $class::registerModelEvent($event, HeimdallObserver::class.'@handle');
            }
        }
    }

    protected function registerValidationRule(): void
    {
        Validator::extend(
            'heimdall',
            fn ($attribute, $value, $rules) => (new HeimdallRule($rules))->passes($attribute, $value),
            Lang::get('validation::validation.heimdall'),
        );
    }
}
