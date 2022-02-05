<?php

namespace PhpLsh\MinHash;

final class MinHash implements MinHashInterface
{
    /**
     * @var HashInterface[]
     */
    private array $hashFunctions;

    public function __construct(array $hashFunctions)
    {
        $this->hashFunctions = $hashFunctions;
    }

    public static function createBasicOfLength(int $length): self
    {
        $hashFunctions = [];
        for ($i = 0; $i < $length; $i++) {
            $hashFunctions[] = new BasicHash();
        }

        return new self($hashFunctions);
    }

    public function minHash(iterable $shingles): array
    {
        $minhashVector = [];

        // Unwind the iterable since we need to loop over it multiple times.
        $shingles = [...$shingles];

        // For every hash function we calculate the minimum hashed shingle value
        // and append it to the minhash vector.
        foreach ($this->hashFunctions as $hashFunction) {
            $minimum = PHP_INT_MAX;
            foreach ($shingles as $shingle) {
                $hash = $hashFunction->hash($shingle);
                if ($hash < $minimum) {
                    $minimum = $hash;
                }
            }

            $minhashVector[] = $minimum;
        }

        return $minhashVector;
    }
}
