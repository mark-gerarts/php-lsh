<?php

namespace PhpLsh\Shingle;

use PHPUnit\Framework\TestCase;

final class WordTokenizerTest extends TestCase
{
    public function testSentencesSmallerThanShingleSizeResultInNoShingles(): void
    {
        $this->assertEmpty($this->shingle("This sentence is six words long", 7));
    }

    public function testSentencesOfSizeEqualToShingleSizeReturnOneShingle(): void
    {
        $string = "This sentence is six words long";

        $this->assertEquals(
            [$string],
            $this->shingle($string, 6)
        );
    }

    public function testAllWhitespaceIsUsedToSeparateWords(): void
    {
        $string = "This    sentence is\n six, words long";

        $this->assertEquals(
            ["This sentence is six words long"],
            $this->shingle($string, 6)
        );
    }

    public function testItShinglesASimpleSentence(): void
    {
        $result = $this->shingle("The quick brown fox", 2);

        $expected = [
            "The quick",
            "quick brown",
            "brown fox"
        ];
        $this->assertEquals($expected, $result);
    }

    private function shingle(string $input, int $shingleSize): array
    {
        $tokenizer = new WordTokenizer();
        $tokens = $tokenizer->tokenize($input, $shingleSize);

        return iterator_to_array($tokens);
    }
}
