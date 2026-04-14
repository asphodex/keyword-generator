<?php

namespace App\ValueObject;

readonly class Word
{
    private const VALID_PREFIX_LETTERS = ['+', '-', '!'];


    public function __construct(
        private string $prefix,
        private string $bare
    )
    {
    }

    public static function fromString(string $raw): self
    {
        if ($raw !== '' && in_array($raw[0], self::VALID_PREFIX_LETTERS, true)) {
            return new self($raw[0], mb_substr($raw, 1));
        }

        return new self('', $raw);
    }

    public function isMinus(): bool
    {
        return $this->prefix === '-';
    }

    public function isShort(): bool
    {
        return mb_strlen($this->bare) <= 2;
    }

    public function hasOperatorPrefix(): bool
    {
        return $this->prefix === '+' || $this->prefix === '!';
    }

    public function withShortWordPrefix(): self
    {
        if ($this->isShort() && !$this->hasOperatorPrefix() && !$this->isMinus()) {
            return new self('+', $this->bare);
        }

        return $this;
    }

    public function getBare(): string
    {
        return $this->bare;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function toString(): string
    {
        return $this->prefix . $this->bare;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
