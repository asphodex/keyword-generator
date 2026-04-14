<?php

namespace App\Tests\unit\ValueObject;

use App\ValueObject\Phrase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PhraseTest extends TestCase
{
    public static function fromStringProvider(): array
    {
        return [
            'regular and minus words' => [
                'Honda CRF -Владивосток',
                2,
                1
            ],
            'all regular' => [
                'Honda CRF 450X',
                3,
                0
            ],
            'all minus' => [
                '-Honda -450X',
                0,
                2
            ],
            'with exclamation and plus letters' => [
                'Honda +CRF !450X',
                3,
                0
            ],
            'empty string' => [
                '',
                0,
                0
            ]
        ];
    }

    #[DataProvider('fromStringProvider')]
    public function testFromString(string $raw, int $regularCount, int $minusCount): void
    {
        $phrase = Phrase::fromString($raw);

        $this->assertCount($regularCount, $phrase->getRegularWords());
        $this->assertCount($minusCount, $phrase->getMinusWords());
    }

    public static function bareRegularWordsSetProvider(): array
    {
        return [
            'plus and exclamation' => [
                '+в Honda !купить -Владивосток',
                ['в', 'Honda', 'купить'],
                ['Владивосток']
            ],
            'plain words' => [
                'Honda CRF',
                ['Honda', 'CRF'],
                []
            ]
        ];
    }

    #[DataProvider('bareRegularWordsSetProvider')]
    public function testBareRegularWordsSet(string $raw, array $expectedKeys, array $excludedKeys): void
    {
        $set = Phrase::fromString($raw)->bareRegularWordsSet();

        foreach ($expectedKeys as $expectedKey) {
            $this->assertArrayHasKey($expectedKey, $set);
        }

        foreach ($excludedKeys as $excludedKey) {
            $this->assertArrayNotHasKey($excludedKey, $set);
        }
    }

    public static function canonicalKeyProvider(): array
    {
        return [
            'same words different order' => [
                'Honda Владивосток продажа',
                'продажа Honda Владивосток'
            ],
            'with minus words' => [
                'Honda -Москва продажа',
                'продажа Honda -Москва'
            ]
        ];
    }

    #[DataProvider('canonicalKeyProvider')]
    public function testCanonicalKeyIsOrderIndependent(string $rawA, string $rawB): void
    {
        $this->assertSame(
            Phrase::fromString($rawA)->canonicalKey(),
            Phrase::fromString($rawB)->canonicalKey(),
        );
    }

    public static function withExtraMinusProvider(): array
    {
        return [
            'new minus words' => [
                'Honda',
                ['CRF', '450X'],
                'Honda -450X -CRF'
            ],
            'skip existing' => [
                'Honda -Москва',
                ['CRF', '450X', 'Москва'],
                'Honda -450X -CRF -Москва'
            ],
            'empty minus words' => [
                'Honda -Москва',
                [],
                'Honda -Москва'
            ]
        ];
    }

    #[DataProvider('withExtraMinusProvider')]
    public function testWithExtraMinus(string $raw, array $extraMinus, string $expected): void
    {
        $result = Phrase::fromString($raw)->withExtraMinus($extraMinus);

        $this->assertSame($expected, $result->toString());
    }

    public static function toStringProvider(): array
    {
        return [
            'minus moves to end' => [
                'Honda -Москва Владивосток',
                'Honda Владивосток -Москва',
            ],
            'empty' => [
                '',
                '',
            ],
            'regular' => [
                '!Honda CRF 450X',
                '!Honda CRF 450X',
            ],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(string $raw, string $expected): void
    {
        $this->assertSame($expected, Phrase::fromString($raw)->toString());
    }
}
