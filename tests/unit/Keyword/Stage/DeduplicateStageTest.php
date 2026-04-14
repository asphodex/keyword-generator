<?php

namespace App\Tests\unit\Keyword\Stage;

use App\Keyword\Stage\DeduplicateStage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;

final class DeduplicateStageTest extends TestCase
{
    private DeduplicateStage $stage;

    protected function setUp(): void
    {
        $this->stage = new DeduplicateStage();
    }

    public static function deduplicateProvider(): array
    {
        return [
            'remove duplicate with different word order' => [
                [
                    'Honda Владивосток продажа',
                    'Владивосток Honda продажа',
                    'продажа Владивосток Honda'
                ],
                ['Honda Владивосток продажа'] // first occurrence
            ],
            'keep unique phrases' => [
                [
                    'Honda Владивосток продажа',
                    'Honda Владивосток покупка',
                    'Honda CRF продажа'
                ],
                [
                    'Honda Владивосток продажа',
                    'Honda Владивосток покупка',
                    'Honda CRF продажа'
                ]
            ],
            'phrases with minus words deduplication' => [
                [
                    'Honda -Москва продажа',
                    'продажа Honda -Москва'
                ],
                ['Honda -Москва продажа']
            ],
            'empty input' => [
                [],
                []
            ]
        ];
    }

    #[DataProvider('deduplicateProvider')]
    public function testDeduplicate($input, array $expected): void
    {
        $this->assertSame($expected, $this->stage->process($input));
    }
}
