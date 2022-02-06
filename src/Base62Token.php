<?php

declare(strict_types=1);

namespace Tenkoma\GithubStyleAuthenticationToken;

use Exception;
use InvalidArgumentException;
use RuntimeException;
use Tuupola\Base62;

class Base62Token
{
    public function __construct(
        private string $prefix,
        private string $random,
        private string $checksum,
    ) {
        if (!self::verify($this->toString())) {
            throw new InvalidArgumentException();
        }
    }

    public function toString(): string
    {
        return $this->prefix . $this->random . $this->checksum;
    }

    public static function verify(string $token): bool
    {
        $parsed = self::parse($token);
        if (empty($parsed)) {
            return false;
        }

        return self::calcChecksum($parsed['random']) === $parsed['checksum'];
    }

    /**
     * @param string $token
     * @return array<string, string>
     */
    private static function parse(string $token): array
    {
        $checksumDigit = 6;
        $pattern = "/\A(?P<prefix>[a-zA-Z0-9_]+_)(?P<random>[a-zA-Z0-9]+)"
            . "(?P<checksum>[a-zA-Z0-9]{{$checksumDigit}})\z/";
        if (preg_match($pattern, $token, $matches) !== 1) {
            return [];
        }

        return [
            'prefix' => $matches['prefix'],
            'random' => $matches['random'],
            'checksum' => $matches['checksum'],
        ];
    }

    private static function calcChecksum(mixed $randomToken): string
    {
        $base62 = new Base62();

        return sprintf('%06s', $base62->encodeInteger(crc32($randomToken)));
    }

    public static function generate(string $prefix, int $lengthOfRandom): self
    {
        $random = self::generatePseudoRandom($lengthOfRandom);

        return new self($prefix, $random, self::calcChecksum($random));
    }

    private static function generatePseudoRandom(int $lengthOfRandom): string
    {
        if ($lengthOfRandom < 1) {
            throw new InvalidArgumentException('length must be an integer greater than zero');
        }

        $chars = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        $stringBuffer = '';
        for ($i = 0; $i < $lengthOfRandom; $i++) {
            try {
                $stringBuffer .= $chars[random_int(0, 61)];
            } catch (Exception $e) {
                throw new RuntimeException(previous: $e);
            }
        }

        return $stringBuffer;
    }

    public static function createFromString(string $token): self
    {
        $parsed = self::parse($token);
        if (empty($parsed)) {
            throw new InvalidArgumentException();
        }

        return new self($parsed['prefix'], $parsed['random'], $parsed['checksum']);
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getRandom(): string
    {
        return $this->random;
    }
}
