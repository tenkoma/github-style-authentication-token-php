<?php

declare(strict_types=1);

namespace Tenkoma\GithubStyleAuthenticationToken\Test\Sample;

use InvalidArgumentException;
use Tenkoma\GithubStyleAuthenticationToken\Base62Token;

class SampleToken
{
    private const PREFIX = 'sample_';
    private const LENGTH_OF_RANDOM = 30;

    public function __construct(private Base62Token $value)
    {
        if ($value->getPrefix() !== self::PREFIX) {
            throw new InvalidArgumentException('invalid prefix');
        }
        if (mb_strlen($value->getRandom()) !== self::LENGTH_OF_RANDOM) {
            throw new InvalidArgumentException('invalid length of random');
        }
    }

    public static function createFromString(string $string): self
    {
        return new self(Base62Token::createFromString($string));
    }

    public static function generate(): self
    {
        return new self(Base62Token::generate(self::PREFIX, self::LENGTH_OF_RANDOM));
    }

    public function toString(): string
    {
        return $this->value->toString();
    }
}
