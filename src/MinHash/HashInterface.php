<?php

namespace PhpLsh\MinHash;

interface HashInterface
{
    public function hash(string $input): int;
}
