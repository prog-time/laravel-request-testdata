<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators;

use Carbon\Carbon;
use Faker\Factory as Faker;

class DatetimeDataGenerator
{

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * @param string $typeData
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @param string|null $format
     * @return string
     */
    public function generate(string $typeData, ?Carbon $startDate, ?Carbon $endDate, ?string $format = null): string
    {
        $dateItem = $this->faker->dateTime();

        if (!empty($startDate) && !empty($endDate)) {
            $dateItem = $this->faker->dateTimeBetween($startDate, $endDate);
        } elseif (!empty($startDate)) {
            $dateItem = $this->faker->dateTimeBetween($startDate, Carbon::parse('+10 years'));
        } elseif (!empty($endDate)) {
            $dateItem = $this->faker->dateTimeBetween(Carbon::parse('-10 years'), $endDate);
        }

        if (!empty($format)) {
            $value = $dateItem->format($format);
        } else {
            switch ($typeData) {
                case 'date':
                    $value = $dateItem->format('Y-m-d');
                    break;

                case 'time':
                    $value = $dateItem->format('H:i:s');
                    break;

                default:
                    $value = $dateItem->format('Y-m-d H:i:s');
                    break;
            }
        }

        return $value ?? '';
    }

}
