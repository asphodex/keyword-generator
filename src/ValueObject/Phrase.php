<?php

namespace App\ValueObject;

readonly class Phrase
{
    public function __construct(
        private array $regularWords,
        private array $minusWords = []
    )
    {
    }

    public static function fromString(string $raw): self
    {
        $parts = preg_split('/\s+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $regularWords = [];
        $minusWords = [];

        foreach ($parts as $part) {
            $word = Word::fromString($part);

            if ($word->isMinus()) {
                $minusWords[] = $word;
            } else {
                $regularWords[] = $word;
            }
        }

        return new self($regularWords, $minusWords);
    }

    public function bareRegularWordsSet(): array
    {
        $set = [];

        foreach ($this->regularWords as $word) {
            $set[$word->getBare()] = true;
        }

        return $set;
    }

    public function canonicalKey(): string
    {
        $all = array_map(
            fn(Word $word): string => $word->toString(),
            [...$this->regularWords, ...$this->minusWords]
        );

        sort($all);

        return implode(' ', $all);
    }

    public function withExtraMinus(array $bareWords): self
    {
        $existing = [];
        foreach ($this->minusWords as $word) {
            $existing[$word->getBare()] = true;
        }

        $newMinusWords = $this->minusWords;
        foreach ($bareWords as $word) {
            if (!isset($existing[$word])) {
                $newMinusWords[] = Word::fromString('-' . $word);
                $existing[$word] = true;
            }
        }

        usort($newMinusWords, fn(Word $a, Word $b): int => strcmp($a->getBare(), $b->getBare()));

        return new self($this->regularWords, $newMinusWords);
    }

    public function getRegularWords(): array
    {
        return $this->regularWords;
    }

    public function getMinusWords(): array
    {
        return $this->minusWords;
    }

    public function toString(): string
    {
        $parts = array_map(
            fn(Word $w): string => $w->toString(),
            [...$this->regularWords, ...$this->minusWords],
        );

        return implode(' ', $parts);
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
