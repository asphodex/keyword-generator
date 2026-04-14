<?php

namespace App\Tests\unit\Keyword\Parser;

use App\Keyword\Parser\TextInputParser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TextInputParserTest extends TestCase
{
    private TextInputParser $parser;

    protected function setUp(): void
    {
        $this->parser = new TextInputParser();
    }

    public static function parseProvider(): array
    {
        return [
            'multiple lines' => [
                "Honda, Honda CRF\n-Владивосток, Приморский край",
                [
                    ['Honda', 'Honda CRF'],
                    ['-Владивосток', 'Приморский край']
                ]
            ],
            'skip empty lines' => [
                "Honda\n\n\n\nВладивосток",
                [
                    ['Honda'],
                    ['Владивосток']
                ]
            ],
            'with whitespaces' => [
                " Honda , CRF \n Владивосток",
                [
                    ['Honda', 'CRF'],
                    ['Владивосток']
                ]
            ],
            'skip empty' => [
                'Honda,,CRF, ',
                [
                    ['Honda', 'CRF'],
                ]
            ],
            'empty input' => [
                '',
                []
            ],
            'only whitespaces' => [
                '   ',
                []
            ],
            'three lines variant per line' => [
                "CRF\nПриморский край\nкупить",
                [
                    ['CRF'],
                    ['Приморский край'],
                    ['купить']
                ]
            ]
        ];
    }

    #[DataProvider('parseProvider')]
    public function testParse(string $input, array $expectedOutput): void
    {
        $this->assertSame($expectedOutput, $this->parser->parse($input));
    }
}
