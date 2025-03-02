<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators;

use Faker\Factory as Faker;

class StringDataGenerator implements DataGeneratorInterface
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
    public function generate(string $typeData, int $min = 10, int $max = 0): mixed
    {
        switch ($typeData) {
            case 'string':
                $value = (!empty($min) || !empty($max)) ? $this->generateRandomString('', $min, $max) : $this->faker->sentence();
                break;

            case 'email':
                $value = $this->faker->email();
                break;

            case 'url':
                $value = $this->faker->url();
                break;

            case 'uuid':
                $value = $this->faker->uuid();
                break;

            case 'phone':
                $value = $this->faker->phoneNumber();
                break;

            case 'address':
                $value = $this->faker->address();
                break;

            case 'ip':
            case 'ipv4':
                $value = $this->faker->ipv4();
                break;

            case 'ipv6':
                $value = $this->faker->ipv6();
                break;

            case 'timezone':
                $value = $this->faker->timezone();
                break;

            case 'mac_address':
                $value = $this->faker->macAddress();
                break;

            default:
                $value = $this->generateRandomString('', $min, $max);
        }

        if (!empty($max) && strlen($value) > $max) {
            $value = substr($value, 0, $max);
        }

        return $value ?? '';
    }

    /**
     * @param string $regex
     * @return string|null
     */
    private function getRegexDelimiter(string $regex): ?string
    {
        $delimiter = $regex[0]; // первый символ
        if ($delimiter === $regex[strlen($regex) - 1] && in_array($delimiter, ['/', '#', '~', '@'])) {
            return $delimiter;
        }
        return null;
    }

    /**
     * @param string $regex
     * @return string
     */
    private function extractCentralPart(string $regex): string
    {
        $delimiter = $this->getRegexDelimiter($regex);
        if (!empty($delimiter)) {
            $regex = trim($regex, $delimiter);
        }
        return preg_replace('/^(\^?)(.*?)(\$?)$/', '$2', $regex);
    }

    /**
     * @param string $regex
     * @param int $min
     * @param int $max
     * @return string
     */
    private function transformRegexWithLength(string $regex, int $min, int $max): string
    {
        $regularCenter = $this->extractCentralPart($regex);

        $regCountVal = "";
        if (!empty($min) && !empty($max)) {
            $regCountVal .= $min .",". $max;
        } elseif ($min > 0) {
            $regCountVal .= $min;
        } elseif ($max > 0) {
            $regCountVal .= $max;
        }

        if (!empty($regCountVal)) {
            $regCountValStr = "{";
            $regCountValStr .= $regCountVal;
            $regCountValStr .= "}";

            return "/(". $regularCenter .")". $regCountValStr ."/";
        } else {
            return $regularCenter;
        }
    }

    /**
     * @param string|null $regex
     * @param int|null $min
     * @param int|null $max
     * @return string
     */
    public function generateRandomString(?string $regex = null, ?int $min = null, ?int $max = null): ?string
    {
        try {
            if (!empty($regex)) {
                if (!empty($min) || !empty($max)) {
                    $regex = $this->transformRegexWithLength($regex, $min, $max);
                }

                $regex = trim($regex, '/\\');

                $randExp = new \RandExp\RandExp($regex);
                $value = $randExp->generate();

                if (empty($max)) {
                    $max = 512;
                }
                if (strlen($value) > $max) {
                    $value = substr($value, 0, $max);
                }

            } else {
                if (empty($max)) {
                    $max = 512;
                }

                $regex = "([a-zA-Z0-9]){". $max ."}";
                $randExp = new \RandExp\RandExp($regex);
                $value = $randExp->generate();
            }

            $currentLength = mb_strlen($value);
            if ($currentLength > $max) {
                $value = substr($value, 0, $max);
            }

            return $value;
        } catch (\Exception $e) {
            return null;
        }
    }

}
