<?php

declare(strict_types=1);

namespace Tenkoma\GithubStyleAuthenticationToken;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use Tuupola\Base62;

abstract class Token implements TokenInterface
{
    final public function __construct(
        private string $prefix,
        private string $random,
        private string $checkSum
    ) {
        if (!static::verify($this->prefix . $this->random . $this->checkSum)) {
            throw new InvalidArgumentException();
        }
    }

    public static function createFromString(string $token): static
    {
        $parsed = self::parse($token);
        if (empty($parsed)) {
            throw new InvalidArgumentException();
        }

        return new static($parsed['prefix'], $parsed['random'], $parsed['checksum']);
    }

    public function toString(): string
    {
        return $this->prefix . $this->random . $this->checkSum;
    }

    public static function generate(): static
    {
        $random = self::generatePseudoRandom();

        return new static(static::getPrefix(), $random, self::calcCheckSum($random));
    }

    public static function verify(string $token): bool
    {
        $parsed = self::parse($token);
        if (empty($parsed)) {
            return false;
        }
        if (self::calcCheckSum($parsed['random']) !== $parsed['checksum']) {
            return false;
        }

        return true;
    }

    /**
     * @param string $token
     * @return array<string, string>
     */
    protected static function parse(string $token): array
    {
        $prefix = static::getPrefix();
        $checkSumDigit = 6;
        $pattern = "/\A(?P<prefix>{$prefix})(?P<random>[a-zA-Z0-9]+)(?P<checksum>.{{$checkSumDigit}})\z/";
        if (preg_match($pattern, $token, $matches) !== 1) {
            return [];
        }

        return [
            'prefix' => $matches['prefix'],
            'random' => $matches['random'],
            'checksum' => $matches['checksum'],
        ];
    }

    protected static function generatePseudoRandom(): string
    {
        $length = static::getLengthOfRandom();
        if ($length < 1) {
            throw new InvalidArgumentException('length must be an integer greater than zero');
        }
        $chars = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $stringBuffer = '';
        for ($i = 0; $i < $length; $i++) {
            try {
                $stringBuffer .= $chars[random_int(0, 61)];
            } catch (Exception $e) {
                throw new RuntimeException(previous: $e);
            }
        }

        return $stringBuffer;
    }

    protected static function calcCheckSum(string $randomToken): string
    {
        $base62 = new Base62();

        return sprintf('%06s', $base62->encodeInteger(crc32($randomToken)));
    }
}
