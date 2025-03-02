<?php

namespace Feature\Unit;

use PHPUnit\Framework\TestCase;
use ProgTime\RequestTestData\Classes\FakeGenerators\ArrayGenerator;

class JsonGeneratorTest extends TestCase
{

    public function test_generate_json(): void
    {
        $minItems = 5;
        $maxItems = 10;
        $generateData = (new ArrayGenerator())->generate('json', 'string', $minItems, $maxItems);

        $this->assertJson($generateData);

        $jsonDecode = json_decode($generateData, true);
        $countItems = count($jsonDecode);

        if ($countItems >= $minItems && $countItems <= $maxItems) {
            $this->assertTrue(true);
        } else {
            $this->assertFalse(true);
        }
    }
}
