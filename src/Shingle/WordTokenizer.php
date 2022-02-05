<?php

namespace PhpLsh\Shingle;

final class WordTokenizer implements TokenizerInterface
{
    public function __construct(private array $stopWords = []) {}

    public function tokenize(string $input, int $shingleSize): iterable
    {
        if ($shingleSize < 1) {
            throw new \InvalidArgumentException('Shingle size needs to be at least 1');
        }

        $words = preg_split('/\W+/', $input);
        $words = array_filter($words);
        if (count($words) < $shingleSize) {
            return [];
        }

        // Sliding window up until the last full shingle.
        $endIndex = count($words) - $shingleSize;
        $currentIndex = 0;
        while ($currentIndex <= $endIndex) {
            $shingleWords = array_slice($words, $currentIndex, $shingleSize);
            yield implode(" ", $shingleWords);
            $currentIndex++;
        }
    }
}
