<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators;

use Faker\Factory as Faker;

class NumberDataGenerator implements DataGeneratorInterface
{

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * @param string $typeData
     * @param int $min
     * @param int $max
     * @return mixed
     */
    public function generate(string $typeData, int $min = 1, int $max = 0): mixed
    {
        if ($min == 0 && $max == 0) {
            return 0;
        } elseif ($min > $max) {
            return 0;
        }

        if (empty($max)) {
            $max = 1000;
        }

        switch ($typeData) {
            case 'integer':
                $value = (int)$this->faker->numberBetween($min, $max);
                break;

            case 'numeric':
            case 'float':
                $value = (float)$this->faker->randomFloat(2, $min, $max);
                break;
        }

        return $value ?? 0;
    }

    /**
     * @param int $multipleOf
     * @param int|null $min
     * @param int|null $max
     * @param int|null $digits
     * @return int
     */
    public function generateMultipleOfNumber(int $multipleOf, ?int $min = 0, ?int $max = 2147483647, ?int $digits = null): int
    {
        $faker = Faker::create();
        $number = $faker->numberBetween($min, $max);

        $number = round($number / $multipleOf) * $multipleOf;
        if (!empty($digits)) {
            while (strlen((string)$number) !== $digits) {
                $number = $faker->numberBetween($min, $max);
                $number = round($number / $multipleOf) * $multipleOf;
            }
        }

        return (int)$number;
    }

}
