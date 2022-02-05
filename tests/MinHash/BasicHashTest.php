<?php

namespace PhpLsh\MinHash;

use PHPUnit\Framework\TestCase;

final class BasicHashTest extends TestCase
{
    public function testItHashesToAnInt(): void
    {
        $hash = new BasicHash();
        $result = $hash->hash('some value');

        $this->assertIsInt($result);
    }

    public function testItHashesTheSameInputToTheSameOutput(): void
    {
        $hash = new BasicHash();

        $result1 = $hash->hash('some value');
        $result2 = $hash->hash('some value');

        $this->assertEquals($result1, $result2);
    }

    public function testItHashesDifferentInputToDifferentOutput(): void
    {
        $hash = new BasicHash();

        $result1 = $hash->hash('value 1');
        $result2 = $hash->hash('value 2');

        $this->assertNotEquals($result1, $result2);
    }

    public function testTwoInstancesGenerateDifferentHashes(): void
    {
        $hash1 = new BasicHash();
        $hash2 = new BasicHash();
        $input = 'some value';

        $result1 = $hash1->hash($input);
        $result2 = $hash2->hash($input);

        $this->assertNotEquals($result1, $result2);
    }
}
