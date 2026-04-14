<?php

namespace App\Keyword\Parser;

final class TextInputParser
{
    public function parse(string $input): array
    {
        $result = [];

        foreach (explode("\n", $input) as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $variants = array_values(
                array_filter(
                    array_map('trim', explode(',', $line)),
                    fn(string $v): bool => $v !== '',
                )
            );

            if ($variants !== []) {
                $result[] = $variants;
            }
        }

        return $result;
    }
}
