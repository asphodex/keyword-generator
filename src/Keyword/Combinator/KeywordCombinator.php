<?php

namespace App\Keyword\Combinator;

final class KeywordCombinator
{
    public function combine(array $sets): array {
        if ($sets === []) {
            return [];
        }

        $result = [''];

        foreach ($sets as $set) {
            $next = [];

            foreach ($result as $existing) {
                foreach ($set as $item) {
                    $next[] = $existing === '' ? $item : "{$existing} {$item}";
                }
            }

            $result = $next;
        }

        return $result;
    }
}
