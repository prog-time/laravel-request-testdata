<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators;

use Faker\Factory as Faker;
use Random\RandomException;

class BooleanDataGenerator
{

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * @param bool $booleanInt
     * @return bool
     * @throws RandomException
     */
    public function generate(bool $booleanInt = false): bool
    {
        if ($booleanInt) {
            $value = random_int(0, 1);
        } else {
            $value = $this->faker->boolean();
        }
        return $value;
    }

}
