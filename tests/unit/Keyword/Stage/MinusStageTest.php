<?php

namespace App\Tests\unit\Keyword\Stage;

use App\Keyword\Stage\MinusStage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MinusStageTest extends TestCase
{
    private MinusStage $stage;

    protected function setUp(): void
    {
        $this->stage = new MinusStage();
    }

    public static function minusProvider(): array
    {
        return [
            'subset phrases get minus words' => [
                ['Honda', 'Honda CRF', 'Honda CRF 450X'],
                ['Honda -450X -CRF', 'Honda CRF -450X', 'Honda CRF 450X']
            ],
            'unchanged overlap' => [
                ['Honda продажа', 'Toyota покупка'],
                ['Honda продажа', 'Toyota покупка']
            ],
            'no duplicate minus words' => [
                ['Honda -CRF', 'Honda CRF'],
                ['Honda -CRF', 'Honda CRF']
            ],
            'already have minus word' => [
                ['Honda -Москва', 'Honda CRF'],
                ['Honda -CRF -Москва', 'Honda CRF']
            ],
            'equal sets are not subsets' => [
                ['Honda CRF', 'Honda CRF'],
                ['Honda CRF', 'Honda CRF'],
            ],
            'words with prefixes' => [
                ['+в Honda', '+в Honda CRF'],
                ['+в Honda -CRF', '+в Honda CRF']
            ],
            'empty input' => [
                [],
                []
            ]
        ];
    }

    #[DataProvider('minusProvider')]
    public function testMinus(array $input, array $expected): void
    {
        $this->assertSame($expected, $this->stage->process($input));
    }
}
