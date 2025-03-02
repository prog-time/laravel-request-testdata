<?php

namespace ProgTime\RequestTestData\Classes;

use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RequestTestingData
{
    /**
     * Faker instance
     * @var Generator
     */
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * @param array $rules
     * @return array
     */
    private function filterParentKeys(array $rules): array
    {
        $keys = array_keys($rules);
        usort($keys, fn($a, $b) => strlen($a) <=> strlen($b));

        $filtered = $rules;
        foreach ($keys as $key) {
            foreach ($keys as $childKey) {
                if ($key !== $childKey && str_starts_with($childKey, $key . '.')) {
                    unset($filtered[$key]);
                    break;
                }
            }
        }

        return $filtered;
    }

    /**
     * @param array $flatArray
     * @return array
     */
    private function buildNestedArray(array $flatArray): array
    {
        $nestedArray = [];

        foreach ($flatArray as $key => $value) {
            $keys = explode('.', $key);
            $temp = &$nestedArray;

            foreach ($keys as $subKey) {
                if ($subKey === '*' || is_numeric($subKey)) {
                    if (!is_array($temp)) {
                        $temp = [];
                    }
                    $temp[] = [];
                    $temp = &$temp[array_key_last($temp)];
                } else {
                    if (!isset($temp[$subKey]) || !is_array($temp[$subKey])) {
                        $temp[$subKey] = [];
                    }
                    $temp = &$temp[$subKey];
                }
            }

            $temp = $value;
        }

        return $nestedArray;
    }

    /**
     * @param array $requestArguments
     * @param FormRequest $requestClass
     * @return array
     */
    public function getCurrentRequestTestingData(array $requestArguments, FormRequest $requestClass): array
    {
        $resultData = [];
        if (!empty($requestArguments)) {
            (new RequestCheckData())->setRequestClass($requestClass);

            $confirmedFields = [];
            foreach ($requestArguments as $keyField => $ruleParams) {
                if (!empty($ruleParams['confirmed'])) {
                    $confirmedFields[] = $keyField;
                }

                $resultData[$keyField] = $this->getFakerData($ruleParams, $keyField);
            }
        }

        if (!empty($confirmedFields)) {
            foreach ($confirmedFields as $confirmedField) {
                $resultData[$confirmedField . '_confirmed'] = $resultData[$confirmedField];
            }
        }

        $filteredData = $this->filterParentKeys($resultData);
        return $this->buildNestedArray($filteredData);
    }

    /**
     * Getting the value from the database
     * @param array $ruleParams
     * @return string|null
     */
    private function getExistingValue(array $ruleParams): ?string
    {
        try {
            if (empty($ruleParams['exists'])) {
                throw new \Exception("Rule 'exists' does not exist");
            }

            $tableParams = explode(',', $ruleParams['exists']);
            $tableName = $tableParams[0];
            $fieldName = $tableParams[1];

            if (!Schema::hasTable($tableName)) {
                throw new \Exception("Table '{$tableName}' does not exist");
            }

            $query = DB::table($tableName)
                ->select($fieldName)
                ->whereNotNull($fieldName);

            $whereFieldName = $tableParams[2] ?? null;
            $whereFieldValue = trim($tableParams[3], '"') ?? null;

            if (!empty($whereFieldName) && !empty($whereFieldValue)) {
                $query = $query->where($whereFieldName, $whereFieldValue);
            }

            $resultData = $query->first();
            if (empty($resultData->$fieldName)) {
                throw new \Exception("Value '{$fieldName}' does not exist");
            }

            return (string)$resultData->$fieldName;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Getting faker data
     * @param array $ruleParams
     * @return mixed
     */
    private function getFakerData(array $ruleParams, string $keyField): mixed
    {
        try {
            if (!empty($ruleParams)) {
                if (!empty($ruleParams['exists'])) {
                    $dataParam = $this->getExistingValue($ruleParams);
                } else {
                    $dataParam = (new RequestCheckData())->getDataParam($ruleParams, $keyField);
                }
            }
            return $dataParam ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

}
