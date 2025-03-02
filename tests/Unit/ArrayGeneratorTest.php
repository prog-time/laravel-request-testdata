<?php

namespace Feature\Unit;

use ProgTime\RequestTestData\Classes\FakeGenerators\ArrayGenerator;
use Tests\TestCase;

class ArrayGeneratorTest extends TestCase
{
    private int $minItems = 5;
    private int $maxItems = 10;

    private function generateArray(string $typeValue): array
    {
        $generateData = (new ArrayGenerator())->generate('array', $typeValue, $this->minItems, $this->maxItems);

        $this->assertIsArray($generateData);

        $countItems = count($generateData);
        if ($countItems >= $this->minItems && $countItems <= $this->maxItems) {
            $this->assertTrue(true);
        } else {
            $this->fail('Error! Count items');
        }

        return $generateData;
    }

    public function test_generate_array_strings(): void
    {
        $typeValue = 'string';
        $generateData = $this->generateArray($typeValue);

        foreach ($generateData as $item) {
            if (!is_string($item)) {
                $this->fail('Error! Item should be a '. $typeValue);
            }
        }

        $this->assertTrue(true);
    }

    public function test_generate_array_integer(): void
    {
        $typeValue = 'integer';
        $generateData = $this->generateArray($typeValue);

        foreach ($generateData as $item) {
            if (!is_integer($item)) {
                $this->fail('Error! Item should be a '. $typeValue);
            }
        }

        $this->assertTrue(true);
    }

}
