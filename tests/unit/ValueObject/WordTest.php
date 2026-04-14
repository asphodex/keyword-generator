<?php

namespace App\Tests\unit\ValueObject;

use App\ValueObject\Word;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class WordTest extends TestCase
{
    public static function fromStringProvider(): array
    {
        return [
            'plain word' => ['Honda', '', 'Honda'],
            'plus prefix' => ['+в', '+', 'в'],
            'minus prefix' => ['-Владивосток', '-', 'Владивосток'],
            'exclamation prefix' => ['!продажа', '!', 'продажа'],
            'empty string' => ['', '', ''],
            'just prefix' => ['+', '+', '']
        ];
    }

    #[DataProvider('fromStringProvider')]
    public function testFromString(string $raw, string $expectedPrefix, string $expectedBare): void
    {
        $word = Word::fromString($raw);

        $this->assertSame($expectedPrefix, $word->getPrefix());
        $this->assertSame($expectedBare, $word->getBare());
    }

    public static function isMinusProvider(): array
    {
        return [
            'minus' => ['-Honda', true],
            'plus' => ['+Honda', false],
            'plain' => ['Honda', false],
        ];
    }

    #[DataProvider('isMinusProvider')]
    public function testIsMinus(string $raw, bool $expected): void
    {
        $this->assertSame($expected, Word::fromString($raw)->isMinus());
    }

    public static function isShortProvider(): array
    {
        return [
            '1 char' => ['в', true],
            '2 chars' => ['по', true],
            '3 chars' => ['абв', false],
            '1 char with prefix' => ['+с', true],
        ];
    }

    #[DataProvider('isShortProvider')]
    public function testIsShort(string $raw, bool $expected): void
    {
        $this->assertSame($expected, Word::fromString($raw)->isShort());
    }

    public static function withShortWordPrefixProvider(): array
    {
        return [
            'short word' => ['на', '+на'],
            'unchanged plus' => ['+в', '+в'],
            'unchanged exclamation' => ['!на', '!на'],
            'unchanged minus' => ['-на', '-на'],
            'long word unchanged' => ['Владивосток', 'Владивосток']
        ];
    }

    #[DataProvider('withShortWordPrefixProvider')]
    public function testWithShortWordPrefix(string $raw, string $expected): void
    {
        $this->assertSame($expected, Word::fromString($raw)->withShortWordPrefix()->toString());
    }

    public static function toStringProvider(): array
    {
        return [
            'with plus' => ['+слово', '+слово'],
            'with minus' => ['-test', '-test'],
            'plain' => ['plain', 'plain'],
        ];
    }

    #[DataProvider('toStringProvider')]
    public function testToString(string $raw, string $expected): void
    {
        $this->assertSame($expected, Word::fromString($raw)->toString());
    }
}
