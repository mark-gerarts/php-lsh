<?php

namespace PhpLsh\Shingle;

/**
 * Classic character-based shingling without any special treatment of
 * whitespace or punctuation.
 *
 * For example: "The quick brown fox" with shingle size 2 becomes "Th", "he",
 * "e ", " q", "qu", ...
 */
final class BasicCharacterTokenizer implements TokenizerInterface
{
    public function tokenize(string $input, int $shingleSize): iterable
    {
        if ($shingleSize < 1) {
            throw new \InvalidArgumentException('Shingle size needs to be at least 1');
        }

        // Sliding window up until the last full shingle.
        $endIndex = mb_strlen($input) - $shingleSize;
        $currentIndex = 0;
        while ($currentIndex <= $endIndex) {
            yield mb_substr($input, $currentIndex, $shingleSize);
            $currentIndex++;
        }
    }
}
