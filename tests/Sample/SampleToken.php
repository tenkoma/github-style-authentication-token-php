<?php

declare(strict_types=1);

namespace Tenkoma\GithubStyleAuthenticationToken\Test\Sample;

use Tenkoma\GithubStyleAuthenticationToken\Token;

class SampleToken extends Token
{
    public static function getPrefix(): string
    {
        return 'sample_';
    }

    public static function getLengthOfRandom(): int
    {
        return 30;
    }
}
