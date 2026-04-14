<?php

namespace App\Keyword\Stage;

use App\ValueObject\Word;

final class NormalizeStage implements KeywordStageInterface
{
    public function process(array $phrases): array
    {
        return array_map($this->normalizePhrase(...), $phrases);
    }

    private function normalizePhrase(string $phrase): string
    {
        $tokens = preg_split('/\s+/', $phrase, -1, PREG_SPLIT_NO_EMPTY);
        $words = [];

        foreach ($tokens as $token) {
            array_push($words, ...$this->splitAndCleanToken($token));
        }

        $regularWords = [];
        $minusWords = [];

        foreach ($words as $word) {
            if ($word->isMinus()) {
                $minusWords[] = $word;
            } else {
                $regularWords[] = $word->withShortWordPrefix();
            }
        }

        $parts = array_map(
            fn(Word $w): string => $w->toString(),
            [...$regularWords, ...$minusWords]
        );

        return implode(' ', $parts);
    }

    private function splitAndCleanToken(string $token): array
    {
        if ($token === '') {
            return [];
        }

        $word = Word::fromString($token);

        $cleaned = preg_replace('/[^\p{L}\p{N}]/u', ' ', $word->getBare());
        $parts = preg_split('/\s+/', $cleaned, -1, PREG_SPLIT_NO_EMPTY);

        if ($parts === []) {
            return [];
        }

        $result = [];
        foreach ($parts as $i => $part) {
            // !CRF-450X можно представить как !CRF 450X, так и !CRF !450X
            // пусть фиксируются оба слова, но если понадобится фиксировать
            // префикс только перед первым словом - добавить проверку
            // $i === 0 ? $word->getPrefix() : '';
            $prefix = $word->getPrefix();
            $result[] = new Word($prefix, $part);
        }

        return $result;
    }
}
