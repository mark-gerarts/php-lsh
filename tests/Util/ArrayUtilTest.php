<?php

namespace PhpLsh\Util;

use PHPUnit\Framework\TestCase;

final class ArrayUtilTest extends TestCase
{
    public function testAnEmptyArrayYieldsNoPairs(): void
    {
        $this->assertEmpty(ArrayUtil::pairs([]));
    }

    /**
     * @dataProvider providePairInputs
     */
    public function testItGeneratesPairs(array $input, array $expectedPairs): void
    {
        $output = ArrayUtil::pairs($input);

        $this->assertEquals($expectedPairs, $output);
    }

    public function providePairInputs(): array
    {
        return [
            [
                [1, 2, 3],
                [[1, 2], [1, 3], [2, 3]]
            ],
            [
                ['a', 'b', 'c', 'd'],
                [['a', 'b'], ['a', 'c'], ['a', 'd'], ['b', 'c'], ['b', 'd'], ['c', 'd']]
            ]
        ];
    }
}
