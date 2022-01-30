<?php

declare(strict_types=1);

namespace Tenkoma\GithubStyleAuthenticationToken;

interface TokenInterface
{
    public static function getPrefix(): string;

    public static function getLengthOfRandom(): int;
}
