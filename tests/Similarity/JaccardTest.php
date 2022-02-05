<?php

namespace PhpLsh\Similarity;

use PHPUnit\Framework\TestCase;

final class JaccardTest extends TestCase
{
    public function testDifferentSetsHaveSimilarityZero(): void
    {
        $set1 = [1, 2, 3];
        $set2 = [5, 6, 7];

        self::assertEquals(0, Jaccard::similarity($set1, $set2));
    }

    public function testIdenticalSetsHaveSimilarityOne(): void
    {
        $set1 = [1, 2, 3];
        $set2 = $set1;

        self::assertEquals(1, Jaccard::similarity($set1, $set2));
    }

    /**
     * @dataProvider provideSetData
     */
    public function testVariousSets(array $set1, array $set2, float $expectedSimilarity): void
    {
        $epsilon = 0.01;

        $similarity = Jaccard::similarity($set1, $set2);

        self::assertTrue(abs($similarity - $expectedSimilarity) < $epsilon);

        $distance = Jaccard::distance($set1, $set2);
        $expectedDistance = 1 - $expectedSimilarity;

        self::assertTrue(abs($distance - $expectedDistance) < $epsilon);
    }

    public function provideSetData(): array
    {
        return [
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                0.9
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                [1, 2, 3, 4, 5, 6, 7, 8, 9, 11],
                0.81
            ],
            [
                [1, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
                [1, 2, 3, 3, 4, 5, 6, 7, 8, 9, 10],
                1
            ],
            [
                [1, 2, 3],
                [1, 8, 9, 10],
                0.16
            ]
        ];
    }
}
