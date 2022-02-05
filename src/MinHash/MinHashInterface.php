<?php

namespace PhpLsh\MinHash;

interface MinHashInterface
{
    public function minHash(iterable $shingles): array;
}
