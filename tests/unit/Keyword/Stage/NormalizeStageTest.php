<?php

namespace App\Tests\unit\Keyword\Stage;

use App\Keyword\Stage\NormalizeStage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class NormalizeStageTest extends TestCase
{
    private NormalizeStage $stage;

    protected function setUp(): void
    {
        $this->stage = new NormalizeStage();
    }

    public static function normalizeProvider(): array
    {
        return [
            'short words get plus' => [
                'Honda Владивосток с пробегом',
                'Honda Владивосток +с пробегом'
            ],
            'invalid letters replaced with spaces' => [
                'CRF-450X',
                'CRF 450X'
            ],
            'minus words moves to end' => [
                'Honda -Владивосток продажа',
                'Honda продажа -Владивосток'
            ],
            'prefix remains on all subwords' => [
                '!CRF-450X', // NormalizeStage.php:59
                '!CRF !450X'
            ],
            'plus prefix' => [
                '+на',
                '+на'
            ],
            'exclamation not doubled with plus' => [
                '!в Honda',
                '!в Honda'
            ],
            'already plus short word unchanged' => [
                '+с пробегом',
                '+с пробегом'
            ],
            'complex phrase' => [
                'Honda CRF-450X Приморский край -Владивосток с пробегом',
                'Honda CRF 450X Приморский край +с пробегом -Владивосток',
            ]
        ];
    }

    #[DataProvider('normalizeProvider')]
    public function testNormalize(string $input, string $expected): void
    {
        $result = $this->stage->process([$input]);

        $this->assertSame([$expected], $result);
    }

    public function testEmptyInput(): void
    {
        $this->assertSame([], $this->stage->process([]));
    }
}
