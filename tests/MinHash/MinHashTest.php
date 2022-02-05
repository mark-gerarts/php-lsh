<?php

namespace PhpLsh\MinHash;

use PhpLsh\Shingle\BasicCharacterTokenizer;
use PhpLsh\Similarity\Jaccard;
use PHPUnit\Framework\TestCase;

final class MinHashTest extends TestCase
{
    public function testItGeneratesASignatureOfLengthEqualToTheNumberOfHashFunctions(): void
    {
        $hashLength = 5;
        $minhash = MinHash::createBasicOfLength($hashLength);
        $result = $minhash->minHash(['ex', 'xa', 'am', 'mp', 'le', 'e ', ' v', 'va', 'al', 'lu', 'ue']);

        $this->assertCount(
            $hashLength,
            $result
        );
    }

    public function testItGeneratesTheSameSignatureForTheSameInput(): void
    {
        $minhash = $this->instantiateWithLength(3);
        $input = ['o', 'n', 'e', 'c', 'h', 'a', 'r'];

        $result1 = $minhash->minHash($input);
        $result2 = $minhash->minHash($input);

        $this->assertEquals($result1, $result2);
    }

    /**
     * @dataProvider provideSimilarInputStrings
     */
    public function testSimilarItemsResultInSimilarHashes(string $input1, string $input2): void
    {
        $tokenizer = new BasicCharacterTokenizer();
        $input1 = [...$tokenizer->tokenize($input1, 2)];
        $input2 = [...$tokenizer->tokenize($input2, 2)];

        // We perform the minhash process 100 times and average the difference
        // in the real similarity and the signature's similarity. Averaged out
        // these should be reasonably close together.
        $differences = [];
        for ($i = 0; $i < 100; $i++) {
            $minhash = MinHash::createBasicOfLength(100);
            $signature1 = $minhash->minHash($input1);
            $signature2 = $minhash->minHash($input2);

            $effectiveSimilarity = Jaccard::similarity($input1, $input2);
            $signatureSimilarity = Jaccard::similarity($signature1, $signature2);
            $differences[] = $effectiveSimilarity - $signatureSimilarity;
        }

        $epsilon = 0.1;
        $averageDistance = array_sum($differences) / 100;

        $this->assertTrue($averageDistance < $epsilon);
    }

    public function provideSimilarInputStrings(): array
    {
        return [
            [
                'The quick brown fox jumps over the lazy dog. Sphinx of black quartz, judge my vow. The five boxing wizards jump quickly.',
                'The quick blue fox jumps over the lazy dog. Sphinx of black quartz, judge my vows. The five boxing wizards jump quickly.'
            ],
            [
                'Ages come and pass, leaving memories that become legend. Legend fades to myth, and even myth is long forgotten when the Age that gave it birth comes again.',
                'Ages come and go, leaving memories that become legends. Legends fade to myth, and even myth is long forgotten when the Age that gave it birth comes again.'
            ],
            [
                'Occaecati dolorem sapiente qui blanditiis occaecati ut et eveniet. Totam laboriosam quod sint molestiae. Eligendi aliquam et est est. Blanditiis quo deleniti quidem perferendis sed.',
            'Occaecati dolores sapiente qui blanditiis occaecati ut et eveniet. Totam laboriosam quod sint molestiae. Eligendi aliquam et est est. Blanditiis quod delenitii quidem perferendis sed.'
            ]
        ];
    }

    private function instantiateWithLength(int $length): MinHash
    {
        $hashFunctions = [];
        for ($i = 0; $i < $length; $i++) {
            $hashFunctions[] = new BasicHash();
        }

        return new MinHash($hashFunctions);
    }
}
