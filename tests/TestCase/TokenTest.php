<?php

declare(strict_types=1);

namespace Tenkoma\GithubStyleAuthenticationToken\Test\TestCase;

use InvalidArgumentException;
use Tenkoma\GithubStyleAuthenticationToken\Test\Sample\SampleToken;
use Tenkoma\GithubStyleAuthenticationToken\Test\TestCase;

class TokenTest extends TestCase
{
    public function testInitialize(): void
    {
        $sut = new SampleToken('sample_', 'C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp', '0EhQTW');
        $this->assertSame('sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW', $sut->toString());
    }

    public function testInitializeInvalidToken(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SampleToken('invalid_', 'C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp', '0EhQTW');
    }

    public function testGenerate(): void
    {
        $sut = SampleToken::generate();
        $this->assertMatchesRegularExpression('/\Asample_[[:alnum:]]+\z/', $sut->toString());
    }

    /**
     * @dataProvider provideVerify
     */
    public function testVerify(string $token, bool $expected): void
    {
        $this->assertSame($expected, SampleToken::verify($token));
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
            'invalid prefix' => [
                'invalid_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW',
                false,
            ],
            'invalid checksum' => [
                'sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGpEEEEEE',
                false,
            ],
        ];
    }

    public function testCreateFromString(): void
    {
        $sut = SampleToken::createFromString('sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW');
        $this->assertSame('sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW', $sut->toString());
    }
}
