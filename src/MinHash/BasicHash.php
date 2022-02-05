<?php

namespace PhpLsh\MinHash;

/**
 * This needs work...
 *
 * The idea is that every time we do `new BasicHash()`, we get an independent
 * hash function ready to use to generate minhash signatures. We achieve this
 * by hashing with cr32b and then XOR'ing with a random value...
 */
final class BasicHash implements HashInterface
{
    private string $seed;

    public function __construct()
    {
        $this->seed = random_bytes(4);
    }

    public function hash(string $input): int
    {
        $hashResult = hash('crc32b', $input, true);
        $uniqueHashResult = $hashResult ^ $this->seed;
        $uniqueHashResult = bin2hex($uniqueHashResult);

        return intval($uniqueHashResult, 16);
    }
}
