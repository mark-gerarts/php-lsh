<?php

namespace PhpLsh\Shingle;

use PHPUnit\Framework\TestCase;

final class BasicCharacterTokenizerTest extends TestCase
{
    public function testStringsSmallerThanShingleSizeResultInNoShingles(): void
    {
        $this->assertEmpty($this->shingle("Hello", 10));
    }

    public function testStringOfSizeEqualToShingleSizeReturnsTheStringItself(): void
    {
        $string = "Input";

        $this->assertEquals(
            [$string],
            $this->shingle($string, strlen($string))
        );
    }

    public function testItShinglesASimpleWord(): void
    {
        $result = $this->shingle("fountain", 4);

        $expected = ["foun", "ount", "unta", "ntai", "tain"];
        $this->assertEquals($expected, $result);
    }

    public function testItTreatsPunctuationAsRegularCharacters(): void
    {
        $input = ".!?, ";

        $this->assertEquals(
            [".!", "!?", "?,", ", "],
            $this->shingle($input, 2)
        );
    }

    private function shingle(string $input, int $shingleSize): array
    {
        $tokenizer = new BasicCharacterTokenizer();
        $tokens = $tokenizer->tokenize($input, $shingleSize);

        return iterator_to_array($tokens);
    }
}
