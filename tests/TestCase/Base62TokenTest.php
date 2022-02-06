<?php

declare(strict_types=1);

namespace Tenkoma\GithubStyleAuthenticationToken\Test\TestCase;

use InvalidArgumentException;
use Tenkoma\GithubStyleAuthenticationToken\Base62Token;
use Tenkoma\GithubStyleAuthenticationToken\Test\TestCase;

class Base62TokenTest extends TestCase
{
    public function testInitialize(): void
    {
        $sut = new Base62Token('sample_', 'C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp', '0EhQTW');
        $this->assertSame('sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW', $sut->toString());
    }

    public function testInitializeInvalidToken(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Base62Token('sample_', 'C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp', 'invali');
    }

    public function testGenerate(): void
    {
        $sut = Base62Token::generate('sample_', 30);
        $this->assertMatchesRegularExpression('/\Asample_[[:alnum:]]+\z/', $sut->toString());
    }

    /**
     * @dataProvider provideVerify
     */
    public function testVerify(string $token, bool $expected): void
    {
        $this->assertSame($expected, Base62Token::verify($token));
    }

    /**
     * @return array<string, mixed>
     */
    public function provideVerify(): array
    {
        return [
            'valid token' => [
                'sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW',
                true,
            ],
            'valid prefix' => [
                'sample_2_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW',
                true,
            ],
            'invalid checksum' => [
                'sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGpEEEEEE',
                false,
            ],
            'invalid prefix' => [
                'sample-C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW',
                false,
            ],
        ];
    }

    public function testCreateFromString(): void
    {
        $sut = Base62Token::createFromString('sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW');
        $this->assertSame('sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW', $sut->toString());
    }

    public function testGetPrefix(): void
    {
        $sut = Base62Token::createFromString('sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW');
        $this->assertSame('sample_', $sut->getPrefix());
    }

    public function testGetRandom(): void
    {
        $sut = Base62Token::createFromString('sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW');
        $this->assertSame('C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp', $sut->getRandom());
    }
}
