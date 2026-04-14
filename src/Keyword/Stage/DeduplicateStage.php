<?php

namespace App\Keyword\Stage;

use App\ValueObject\Phrase;

final class DeduplicateStage implements KeywordStageInterface
{
    public function process(array $phrases): array
    {
        $seen = [];
        $result = [];

        foreach ($phrases as $raw) {
            $phrase = Phrase::fromString($raw);
            $key = $phrase->canonicalKey();

            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[] = $raw;
            }
        }

        return $result;
    }
}
