<?php

declare(strict_types=1);

namespace AMgrade\Heimdall\Services;

use Illuminate\Support\Facades\Config;

use function array_flip;
use function preg_match;
use function str_ends_with;

use const false;
use const true;

class HeimdallService
{
    protected array $config = [];

    public function __construct()
    {
        $this->config = Config::get('heimdall');
    }

    public function fullMatch(string $value): bool
    {
        $fullMatches = array_flip($this->config['matches']['full'] ?? []);

        return isset($fullMatches[$value]);
    }

    public function domainMatch(string $value): bool
    {
        foreach ($this->config['matches']['domain'] ?? [] as $domain) {
            if (str_ends_with($value, "@{$domain}")) {
                return true;
            }
        }

        return false;
    }

    public function regexpMatch(string $value): bool
    {
        foreach ($this->config['matches']['regexp'] ?? [] as $regexp) {
            if (preg_match($regexp, $value)) {
                return true;
            }
        }

        return false;
    }

    public function allMatch(string $value): bool
    {
        return $this->fullMatch($value) ||
            $this->domainMatch($value) ||
            $this->regexpMatch($value);
    }
}
