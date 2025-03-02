<?php

namespace Feature\Unit;

use PHPUnit\Framework\TestCase;
use ProgTime\RequestTestData\Classes\FakeGenerators\NumberDataGenerator;

class NumberDataGeneratorTest extends TestCase
{
    private int $min = 0;
    private int $max = 100;

    public function test_generate_integer(): void
    {
        $generateData = (new NumberDataGenerator())->generate('integer');
        $this->assertIsInt($generateData);
    }

    public function test_generate_range_integer(): void
    {
        $generateData = (new NumberDataGenerator())->generate('integer', $this->min, $this->max);
        $this->assertIsInt($generateData);

        if ($generateData >= $this->min &&
            $generateData <= $this->max) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }

    public function test_generate_range_numeric(): void
    {
        $generateData = (new NumberDataGenerator())->generate('numeric', $this->min, $this->max);
        $this->assertIsFloat($generateData);

        if ($generateData >= $this->min &&
            $generateData <= $this->max) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }

    public function test_generate_range_float(): void
    {
        $generateData = (new NumberDataGenerator())->generate('float', $this->min, $this->max);
        $this->assertIsFloat($generateData);

        if ($generateData >= $this->min &&
            $generateData <= $this->max) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }

    public function test_generate_range_zero(): void
    {
        $generateData = (new NumberDataGenerator())->generate('integer', 0, 0);
        $this->assertIsInt($generateData);

        if ($generateData === 0) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }

    public function test_min_greater_max(): void
    {
        $generateData = (new NumberDataGenerator())->generate('integer', 3, 2);
        $this->assertIsInt($generateData);

        if ($generateData === 0) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }

}
