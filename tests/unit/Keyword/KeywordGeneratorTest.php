<?php

namespace App\Tests\unit;

use App\Keyword\Combinator\KeywordCombinator;
use App\Keyword\KeywordGenerator;
use App\Keyword\Parser\TextInputParser;
use App\Keyword\Stage\DeduplicateStage;
use App\Keyword\Stage\MinusStage;
use App\Keyword\Stage\NormalizeStage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class KeywordGeneratorTest extends TestCase
{
    private KeywordGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new KeywordGenerator(
            new TextInputParser(),
            new KeywordCombinator(),
            [
                new NormalizeStage(),
                new DeduplicateStage(),
                new MinusStage(),
            ],
        );
    }

    public static function generateProvider(): array
    {
        return [
            'empty input' => [
                '',
                [],
            ],
            'single line' => [
                'Honda, Toyota',
                ['Honda', 'Toyota'],
            ],
            'single line with subset' => [
                'Honda, Honda CRF',
                ['Honda -CRF', 'Honda CRF'],
            ],
            'short words get plus' => [
                "Honda\nВладивосток\nс пробегом",
                ['Honda Владивосток +с пробегом'],
            ],
            'invalid chars replaced' => [
                "Honda CRF-450X\nпродажа",
                ['Honda CRF 450X продажа'],
            ],
            'minus words always at end' => [
                "Honda\nПриморский край -Владивосток\nпродажа",
                ['Honda Приморский край продажа -Владивосток'],
            ],
            'cross-minus prevents competition' => [
                "Honda, Honda CRF, Honda CRF-450X\nпродажа",
                [
                    'Honda продажа -450X -CRF',
                    'Honda CRF продажа -450X',
                    'Honda CRF 450X продажа',
                ],
            ]
        ];
    }

    #[DataProvider('generateProvider')]
    public function testGenerate(string $input, array $expected): void
    {
        $this->assertSame($expected, $this->generator->generate($input));
    }
}
