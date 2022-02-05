<?php

namespace PhpLsh\Shingle;

interface TokenizerInterface
{
    public function tokenize(string $input, int $shingleSize): iterable;
}
