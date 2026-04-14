<?php

namespace App\Keyword;

use App\Keyword\Combinator\KeywordCombinator;
use App\Keyword\Parser\TextInputParser;

final readonly class KeywordGenerator
{
    public function __construct(
        private TextInputParser   $parser,
        private KeywordCombinator $combinator,
        private array             $stages
    )
    {
    }

    public function generate(string $input): array
    {
        $sets = $this->parser->parse($input);
        $phrases = $this->combinator->combine($sets);

        foreach ($this->stages as $stage) {
            $phrases = $stage->process($phrases);
        }

        return $phrases;
    }
}
