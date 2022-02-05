<?php

namespace PhpLsh;

use PHPUnit\Framework\TestCase;

final class LSHTest extends TestCase
{
    public function testDifferingInputsLeadToNoCandidates(): void
    {
        $inputs = [
            'The quick brown fox jumps over the lazy dog. Sphinx of black quartz, judge my vow. The five boxing wizards jump quickly.',
            'Ages come and pass, leaving memories that become legend. Legend fades to myth, and even myth is long forgotten when the Age that gave it birth comes again.',
            'Occaecati dolorem sapiente qui blanditiis occaecati ut et eveniet. Totam laboriosam quod sint molestiae. Eligendi aliquam et est est. Blanditiis quo deleniti quidem perferendis sed.'
        ];

        $lsh = LSH::createWithDefaults();
        $candidates = $lsh->findCandidateItems($inputs);

        $this->assertEmpty($candidates);
    }

    public function testItCanDetectSimilarCandidates(): void
    {
        $inputs = [
            'The quick brown fox jumps over the lazy dog. Sphinx of black quartz, judge my vow. The five boxing wizards jump quickly.',
            'Ages come and pass, leaving memories that become legend. Legend fades to myth, and even myth is long forgotten when the Age that gave it birth comes again.',
            'Occaecati dolorem sapiente qui blanditiis occaecati ut et eveniet. Totam laboriosam quod sint molestiae. Eligendi aliquam et est est. Blanditiis quo deleniti quidem perferendis sed.',
            'The cunning brown fox jumps over the lazy dog. Sphinx of black quartz, judge my vow. The five boxing wizards jump quickly.'
        ];

        // We can be wrong sometimes - it is random after all, but it should be
        // correct a reasonable number of times.
        $equalCount = 0;
        for ($i = 0; $i < 10; $i++) {
            $lsh = LSH::createWithDefaults();
            $candidates = $lsh->findCandidateItems($inputs);
            if ($candidates === [[0, 3]]) {
                $equalCount++;
            }
        }

        $this->assertTrue($equalCount >= 9);
    }
}
