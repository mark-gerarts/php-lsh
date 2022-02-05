<?php

namespace PhpLsh;

use PhpLsh\MinHash\BasicHash;
use PhpLsh\MinHash\HashInterface;
use PhpLsh\MinHash\MinHash;
use PhpLsh\Shingle\BasicCharacterTokenizer;
use PhpLsh\Shingle\TokenizerInterface;

class LSH
{
    public function __construct(
        private TokenizerInterface $tokenizer,
        private MinHash $minHash,
        private HashInterface $hash
    ) {}

    public static function createWithDefaults(int $signatureLength = 100): self
    {
        return new self(
            new BasicCharacterTokenizer(),
            MinHash::createBasicOfLength($signatureLength),
            new BasicHash()
        );
    }

    /**
     * Returns a list of tuples that represent candidate similar pairs. Each
     * tuple contains the indices of the candidates.
     */
    public function findCandidateItems(
        iterable $inputs,
        int $numberOfBands = 5,
        int $numberOfRows = 20,
        float $treshold = 0.8,
        int $shingleSize = 5,
    ): array {
        $signatures = $this->generateMinhashSignatures($inputs, $shingleSize);
        if ($signatures === []) {
            return [];
        }

        $n = count($signatures[array_key_first($signatures)]);
        $this->validateBandsAndRows($numberOfBands, $numberOfRows, $n);

        $buckets = $this->hashBandsToBuckets($signatures, $numberOfBands);
        $matchingBandCounts = $this->countPairs($buckets);

        return $this->getPairsWithCountAboveTreshold(
            $matchingBandCounts,
            $treshold * $numberOfBands
        );
    }

    private function generateMinhashSignatures(
        iterable $inputs,
        int $shingleSize
    ): array {
        $signatures = [];
        foreach ($inputs as $key => $input) {
            $shingles = $this->tokenizer->tokenize($input, $shingleSize);
            $signatures[$key] = $this->minHash->minHash($shingles);
        }

        return $signatures;
    }

    private function validateBandsAndRows(int $b, int $r, int $n): void {
        if ($b * $r !== $n) {
            throw new \InvalidArgumentException(sprintf(
                'bands * rows should equal the minhash signature length. You provided bands=%s, rows=%s, and n=%s',
                $b,
                $r,
                $n
            ));
        }
    }

    private function hashBandsToBuckets(array $signatures, int $numberOfBands): array
    {
        $buckets = [];

        foreach ($signatures as $key => $signature) {
            $bands = array_chunk($signature, $numberOfBands);
            $bands = array_map(fn ($band) => implode('', $band), $bands);

            foreach ($bands as $bandIndex => $band) {
                $hashedValue = $this->hash->hash($band);
                $buckets[$bandIndex][$hashedValue][] = $key;
            }
        }

        return $buckets;
    }

    private function countPairs(array $buckets): array
    {
        $matchingBandCounts = [];

        foreach ($buckets as $bucket) {
            foreach ($bucket as $matchingItems) {
                if (count($matchingItems) < 2) {
                    continue;
                }

                // @todo:
                // Generate pairs here because there can be more than 2 matching
                // items in a bucket.
                sort($matchingItems);
                [$a, $b] = $matchingItems;
                if (!isset($matchingBandCounts[$a])) {
                    $matchingBandCounts[$a] = [];
                }
                if (!isset($matchingBandCounts[$a][$b])) {
                    $matchingBandCounts[$a][$b] = 0;
                }

                $matchingBandCounts[$a][$b]++;
            }
        }


        return $matchingBandCounts;
    }

    private function getPairsWithCountAboveTreshold(array $counts, int $minimumCount): array
    {
        $candidates = [];

        foreach ($counts as $key1 => $value) {
            foreach ($value as $key2 => $count) {
                if ($count > $minimumCount) {
                    $candidates[] = [$key1, $key2];
                }
            }
        }

        return $candidates;
    }
}
