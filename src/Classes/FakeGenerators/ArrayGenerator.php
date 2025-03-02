<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators;

use ProgTime\RequestTestData\Classes\RequestCheckData;
use Faker\Factory as Faker;

class ArrayGenerator
{

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * @param string $typeData
     * @param string|array $eachType
     * @param int|null $min
     * @param int|null $max
     * @return array|false|string
     */
    public function generate(string $typeData, string|array $eachType = 'string', ?int $min = null, ?int $max = 1)
    {
        $countVal = !empty($min) ? $min : $max;
        $value = [];

        switch ($typeData) {
            case 'array':
                for ($i = 1; $i <= $countVal; $i++) {
                    if (is_string($eachType)) {
                        $value[] = (new RequestCheckData())->getDataParam([
                            $eachType => true
                        ]);
                    } elseif (is_array($eachType)) {
                        $value[] = (new RequestCheckData())->getDataParam($eachType);
                    }
                }
                break;

            case 'json':
                for ($i = 1; $i <= $countVal; $i++) {
                    $value[$this->faker->word()] = $this->faker->word();
                }
                $value = json_encode($value);
                break;
        }

        return $value;
    }

}
