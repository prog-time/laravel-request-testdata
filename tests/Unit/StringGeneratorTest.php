<?php

namespace Feature\Unit;

use PHPUnit\Framework\TestCase;
use ProgTime\RequestTestData\Classes\FakeGenerators\StringDataGenerator;
use function Tests\Feature\dump;

class StringGeneratorTest extends TestCase
{

    public function test_generate_string(): void
    {
        $minItems = 20;
        $maxItems = 100;

        $generateData = (new StringDataGenerator())->generate('', $minItems, $maxItems);
        $this->assertIsString($generateData);

        $countItems = strlen($generateData);
        if ($countItems >= $minItems && $countItems <= $maxItems) {
            $this->assertTrue(true);
        } else {
            $this->fail(true);
        }
    }

    public function test_generate_string_regexp(): void
    {
        $listRegexp = [
            // A set of letters and numbers (Latin)
            "/^[a-zA-Z0-9]+$/",
            // Russian phone
            "/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/",
            // HEX code
            "/^#?([a-f0-9]{6}|[a-f0-9]{3})$/",
            // email
            "/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/",
            // URL
            "/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/",
            // IP Address
            "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/",
        ];

        foreach ($listRegexp as $regexp) {
            $generateData = (new StringDataGenerator())->generateRandomString($regexp);
            $this->assertIsString($generateData);

            if (preg_match($regexp, $generateData)) {
                $this->assertTrue(true);
            } else {
                dump($generateData);
                $this->fail(true);
            }
        }
    }



}
