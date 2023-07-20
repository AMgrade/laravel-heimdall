<?php

declare(strict_types=1);

namespace AMgrade\Heimdall\Validation\Rules;

use AMgrade\Heimdall\Services\HeimdallService;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

use function array_flip;
use function array_keys;
use function in_array;
use function method_exists;

use const false;
use const null;
use const true;

class HeimdallRule implements Rule
{
    protected string $allRuleType = 'all';

    protected array $ruleTypes = [];

    protected HeimdallService $service;

    public function __construct(array $ruleTypes = ['all'])
    {
        $this->ruleTypes = $ruleTypes;
        $this->service = Container::getInstance()->make(HeimdallService::class);
    }

    /**
     * @param string $attribute
     * @param string|null $value
     */
    public function passes($attribute, $value): bool
    {
        if (null === $value || '' === $value) {
            return true;
        }

        if (null !== ($result = $this->checkEmptyRuleTypes($value))) {
            return $result;
        }

        $ruleTypes = array_flip($this->ruleTypes);

        if (null !== ($result = $this->checkAllRuleType($value, $ruleTypes))) {
            return $result;
        }

        return $this->checkRuleTypes($ruleTypes, $value);
    }

    public function message(): string
    {
        return Lang::get('validation::validation.heimdall');
    }

    protected function checkEmptyRuleTypes(string $value): ?bool
    {
        return empty($this->ruleTypes) || in_array(null, $this->ruleTypes, true)
            ? $this->validate($value, $this->allRuleType)
            : null;
    }

    protected function checkAllRuleType(string $value, array $ruleTypes): ?bool
    {
        return isset($ruleTypes[$this->allRuleType])
            ? $this->validate($value, $this->allRuleType)
            : null;
    }

    protected function checkRuleTypes(array $ruleTypes, string $value): ?bool
    {
        $ruleTypeValues = array_flip(
            array_keys(Config::get('heimdall.matches', [])),
        );

        foreach ($ruleTypes as $ruleType => $key) {
            if (!isset($ruleTypeValues[$ruleType])) {
                continue;
            }

            if (!$this->validate($value, $ruleType)) {
                return false;
            }
        }

        return true;
    }

    protected function validate(string $value, string $ruleType): bool
    {
        $method = "{$ruleType}Match";

        return method_exists($this->service, $method)
            ? !$this->service->{$method}($value)
            : true;
    }
}
