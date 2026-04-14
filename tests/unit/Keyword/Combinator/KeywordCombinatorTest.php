<?php

namespace App\Tests\unit\Keyword\Combinator;

use App\Keyword\Combinator\KeywordCombinator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class KeywordCombinatorTest extends TestCase
{
    private KeywordCombinator $combinator;

    protected function setUp(): void
    {
        $this->combinator = new KeywordCombinator();
    }

    public static function combineProvider(): array
    {
        return [
            'no sets' => [
                [],
                []
            ],
            'single set' => [
                [['A', 'B']],
                ['A', 'B']
            ],
            'two sets' => [
                [['A', 'B'], ['1', '2']],
                ['A 1', 'A 2', 'B 1', 'B 2'],
            ],
            'three sets with single element' => [
                [['A'], ['B'], ['1']],
                ['A B 1']
            ],
            'three sets with multiple elements' => [
                [['A', 'B'], ['C', 'D'], ['1']],
                ['A C 1', 'A D 1', 'B C 1', 'B D 1']
            ],
            'set with single element' => [
                [['A', 'B'], ['1']],
                ['A 1', 'B 1']
            ]
        ];
    }

    #[DataProvider('combineProvider')]
    public function testCombine(array $sets, array $expected): void
    {
        $this->assertSame($expected, $this->combinator->combine($sets));
    }
}
