<?php

namespace App\Keyword\Stage;

use App\ValueObject\Phrase;

final class MinusStage implements KeywordStageInterface
{
    public function process(array $phrases): array
    {
        $items = array_map(Phrase::fromString(...), $phrases);
        $result = [];

        foreach ($items as $i => $phraseA) {
            $setA = $phraseA->bareRegularWordsSet();
            $extraMinus = [];

            foreach ($items as $j => $phraseB) {
                if ($i === $j) {
                    continue;
                }

                $setB = $phraseB->bareRegularWordsSet();

                if ($this->isStrictSubset($setA, $setB)) {
                    foreach ($setB as $word => $_) {
                        if (!isset($setA[$word])) {
                            $extraMinus[] = $word;
                        }
                    }
                }
            }

            if ($extraMinus !== []) {
                $phraseA = $phraseA->withExtraMinus($extraMinus);
            }

            $result[] = $phraseA->toString();
        }

        return $result;
    }

    private function isStrictSubset(array $a, array $b): bool
    {
        if (count($a) >= count($b)) {
            return false;
        }

        foreach ($a as $word => $_) {
            if (!isset($b[$word])) {
                return false;
            }
        }

        return true;
    }
}
