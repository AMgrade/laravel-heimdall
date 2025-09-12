<?php

declare(strict_types=1);

namespace AMgrade\Heimdall\Services;

interface HeimdallServiceInterface
{
    public function fullMatch(string $value): bool;

    public function domainMatch(string $value): bool;

    public function regexpMatch(string $value): bool;

    public function allMatch(string $value): bool;
}
