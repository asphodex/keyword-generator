<?php

namespace App\Keyword\Stage;

interface KeywordStageInterface
{
    public function process(array $phrases): array;
}
