<?php

namespace ProgTime\RequestTestData\Classes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Config;
use ProgTime\RequestTestData\Classes\FakeGenerators\ArrayGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\BooleanDataGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\DatetimeDataGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\FileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\NumberDataGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\StringDataGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\UniqueValueGenerator;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Faker\Generator;

class RequestCheckData
{
    protected Generator $faker;

    private static ?FormRequest $requestClass = null;

    public function __construct()
    {
        $this->faker = Faker::create(); // Создаём экземпляр Faker
    }

    public function getRequestClass(): ?FormRequest
    {
        return self::$requestClass;
    }

    public function setRequestClass(FormRequest $requestClass): void
    {
        if (self::$requestClass === null) {
            self::$requestClass = $requestClass;
        }
    }

    /**
     * @param array $ruleParams
     * @return string
     */
    private function getTypeData(array $ruleParams): string
    {
        $fileListTypes = [
            'string',
            'text',
            'email',
            'url',
            'uuid',
            'phone',
            'address',
            'integer',
            'numeric',
            'boolean',
            'date',
            'datetime',
            'time',
            'image',
            'file',
            'array',
            'json',
            'ip',
            'ipv4',
            'ipv6',
            'timezone',
            'mac_address',
        ];

        if (in_array('digits', $ruleParams) ||
            in_array('multiple_of', $ruleParams)) {
            $paramType = 'integer';
        } elseif (in_array('boolean', $ruleParams) && in_array('integer', $ruleParams) ||
            in_array('boolean', $ruleParams) && in_array('numeric', $ruleParams)) {
            $paramType = 'boolean';
        } else {
            $paramType = array_intersect($fileListTypes, $ruleParams);
            $paramType = array_shift($paramType);
        }

        return $paramType ?? 'string';
    }

    /**
     * @param array $ruleParams
     * @return int
     */
    private function getMinValue(array $ruleParams): int
    {
        $minvalue = 0;
        if (!empty($ruleParams['digits'])) {
            $zeros = (int) $ruleParams['digits'] - 1;
            $minvalue = (int) ('1' . str_repeat('0', $zeros));
        } elseif (!empty($ruleParams['min'])) {
            $minvalue = (int) $ruleParams['min'];
        }
        return $minvalue;
    }

    /**
     * @param array $ruleParams
     * @return int
     */
    private function getMaxValue(array $ruleParams): int
    {
        $maxvalue = (!empty($ruleParams['min'])) ? (int) $ruleParams['min']:  0;
        if (!empty($ruleParams['digits'])) {
            $zeros = (int) $ruleParams['digits'] - 1;
            $maxvalue = (int) ('9' . str_repeat('9', $zeros));
        } else if (!empty($ruleParams['max'])) {
            $maxvalue = (int) $ruleParams['max'];
        }
        return $maxvalue;
    }

    /**
     * @param array $ruleParams
     * @param int $min
     * @param int $max
     * @return mixed
     */
    private function getValueStart(?string $keyField, array $ruleParams, int $min, int $max): mixed
    {
        try {
            $value = null;
            if (!empty($ruleParams['in']) && empty($ruleParams['array'])) {
                $value = $ruleParams['in'][array_rand($ruleParams['in'])];
            } elseif (!empty($ruleParams['regex'])) {
                $value = (new StringDataGenerator())->generateRandomString($ruleParams['regex'], $min, $max);
            } elseif (!empty($ruleParams['multiple_of'])) {
                $value = (new NumberDataGenerator())->generateMultipleOfNumber($ruleParams['multiple_of'], $min, $max, $ruleParams['digits'] ?? null);
            } elseif (isset($ruleParams['boolean']) && isset($ruleParams['integer']) ||
                isset($ruleParams['boolean']) && isset($ruleParams['numeric'])) {
                $value = (new BooleanDataGenerator())->generate(true);
            } elseif (isset($ruleParams['boolean'])) {
                $value = (new BooleanDataGenerator())->generate();
            }

            return $value;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array $ruleParams
     * @param mixed $value
     * @return mixed
     */
    private function getComplexValueEnd(array $ruleParams, mixed $value): mixed
    {
        if (!empty($value)) {
            if (is_string($value)) {
                if (!empty($ruleParams['starts_with'])) {
                    $length = mb_strlen($ruleParams['starts_with']);
                    $value = preg_replace('/^.{'.$length.'}/', $ruleParams['starts_with'], $value);
                }

                if (!empty($ruleParams['ends_with'])) {
                    $length = mb_strlen($ruleParams['ends_with']);
                    $value = preg_replace('/.{'.$length.'}$/', $ruleParams['ends_with'], $value);
                }
            }
        }

        return $value;
    }

    /**
     * @param array $ruleParams
     * @param int $min
     * @param int $max
     * @return mixed
     * @throws \Exception
     */
    public function getValueForField(array $ruleParams, int $min, int $max): mixed
    {
        $typeData = $this->getTypeData(array_keys($ruleParams));

        $value = null;
        if (in_array($typeData, FakerDataTypes::TYPE_TEXT)) {
            $value = (new StringDataGenerator())->generate($typeData, $min, $max);
        } elseif (in_array($typeData, FakerDataTypes::TYPE_NUMERIC)) {
            $value = (int)(new NumberDataGenerator())->generate($typeData, $min, $max);
        } elseif (in_array($typeData, FakerDataTypes::TYPE_DATE)) {
            $startDate = !empty($ruleParams['after']) || !empty($ruleParams['after_or_equal']) ? Carbon::parse($ruleParams['after'] ?? $ruleParams['after_or_equal']) : null;
            $endDate = !empty($ruleParams['before']) || !empty($ruleParams['before_or_equal']) ? Carbon::parse($ruleParams['before'] ?? $ruleParams['before_or_equal']) : null;
            $format = $ruleParams['date_format'] ?? "Y-m-d H:i:s";

            $value = (new DatetimeDataGenerator())->generate($typeData, $startDate, $endDate, $format);
        } elseif (in_array($typeData, FakerDataTypes::TYPE_FILE)) {
            if ($typeData === 'image') {
                $ruleParams['mimes'] = [
                    'jpg',
                    'png',
                ];
            }

            $fileFormats = FakerDataTypes::TYPE_FILE_FORMAT;
            $currentTypes = !empty($ruleParams['mimes']) ? array_values(
                array_intersect(
                    array_keys($fileFormats),
                    array_values($ruleParams['mimes'])
                )
            ) : array_keys($fileFormats);

            if (!empty($currentTypes)) {
                $value = (new FileGenerator())->generate($currentTypes[0], $max, $ruleParams);
            }

        } elseif (in_array($typeData, FakerDataTypes::TYPE_RESOURCE)) {
            if (!empty($ruleParams['in'])) {
                $value = array_values($ruleParams['in']);
            } else {
                $min = !empty($ruleParams['min']) ? $ruleParams['min'] : 0;
                $max = !empty($ruleParams['max']) ? $ruleParams['max'] : 1;
                $eachParams = $ruleParams['each'] ?? 'string';

                $value = (new ArrayGenerator())->generate($typeData, $eachParams, $min, $max);
            }
        } else {
            $value = true;
        }

        if ($typeData === 'string' && !empty($ruleParams['regex'])) {
            $value = (new StringDataGenerator())->generateRandomString($ruleParams['regex'], $min, $max);
        }

        return $value;
    }

    /**
     * @param array $ruleParams
     * @param int $min
     * @param int $max
     * @param string|null $keyField
     * @return mixed
     */
    private function getUniqueValue(array $ruleParams, int $min, int $max, ?string $keyField = null): mixed
    {
        $value = null;
        if (!empty($ruleParams['unique'])) {
            $typeData = $this->getTypeData(array_keys($ruleParams));
            $value = (new UniqueValueGenerator())->generate($typeData, $keyField, $ruleParams, $min, $max);
        }
        return $value;
    }

    /**
     * @param array $ruleParams
     * @param string|null $keyField
     * @return mixed
     */
    public function getDataParam(array $ruleParams, ?string $keyField = null): mixed
    {
        try {
            $min = $this->getMinValue($ruleParams) ?? null;
            $max = $this->getMaxValue($ruleParams) ?? null;

            $requestClass = $this->getRequestClass();
            if ($requestClass !== null) {
                if (method_exists($requestClass, 'requestTestData')) {
                    $configRequestFaker = $requestClass->requestTestData();
                    if (!empty($configRequestFaker[$keyField])) {
                        return $configRequestFaker[$keyField];
                    }
                }
            }

            $value = $this->getUniqueValue($ruleParams, $min, $max, $keyField);
            if (!isset($value)) {
                $value = $this->getValueStart($keyField, $ruleParams, $min, $max);

                if (!isset($value)) {
                    $value = $this->getValueForField($ruleParams, $min, $max);
                }
            }

            return $this->getComplexValueEnd($ruleParams, $value);
        } catch (\Exception $e) {
            return null;
        }
    }

}
