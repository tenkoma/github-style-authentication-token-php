<?php

declare(strict_types=1);

namespace Tenkoma\GithubStyleAuthenticationToken\Test\TestCase\Sample;

use Tenkoma\GithubStyleAuthenticationToken\Base62Token;
use Tenkoma\GithubStyleAuthenticationToken\Test\Sample\SampleToken;
use Tenkoma\GithubStyleAuthenticationToken\Test\TestCase;

class SampleTokenTest extends TestCase
{
    public function testInitialize(): void
    {
        $sut = new SampleToken(Base62Token::generate('sample_', 30));
        $this->assertInstanceOf(SampleToken::class, $sut);
    }

    public function testCreateFromString(): void
    {
        $sut = SampleToken::createFromString('sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW');
        $this->assertSame('sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW', $sut->toString());
    }

    /**
     * @testWith ["invalid_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGp0EhQTW"]
     *           ["sample_C5e0bWjTFDtOWCfSPjjKPOUWhQpBGpinvali"]
     */
    public function testCreateFromStringFail(string $invalidToken): void
    {
        $this->expectException(\InvalidArgumentException::class);
        SampleToken::createFromString($invalidToken);
    }

    public function testGenerate(): void
    {
        $sut = SampleToken::generate();
        $this->assertMatchesRegularExpression('/\Asample_[[:alnum:]]+\z/', $sut->toString());
    }
}
