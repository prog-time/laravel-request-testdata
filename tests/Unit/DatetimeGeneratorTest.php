<?php

namespace Feature\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use ProgTime\RequestTestData\Classes\FakeGenerators\DatetimeDataGenerator;

class DatetimeGeneratorTest extends TestCase
{
    private string $minValue = '01.01.2020';
    private string $maxValue = '01.02.2020';

    public function test_generate_range_date(): void
    {
        $startDate = Carbon::parse($this->minValue);
        $endDate = Carbon::parse($this->maxValue);

        $generateData = (new DatetimeDataGenerator())->generate('date', $startDate, $endDate);
        $this->assertIsString($generateData);

        $dateToCheck = Carbon::parse($generateData);
        if ($dateToCheck->between($startDate, $endDate)) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }

    public function test_generate_date_format(): void
    {
        $startDate = Carbon::parse($this->minValue);
        $endDate = Carbon::parse($this->maxValue);

        $format = "d.m.Y H:i:s";
        $regExpFormat = "/^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}:\d{2}$/";
        $generateData = (new DatetimeDataGenerator())->generate('date', $startDate, $endDate, $format);
        $this->assertIsString($generateData);

        if (preg_match($regExpFormat, $generateData)) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }
}
