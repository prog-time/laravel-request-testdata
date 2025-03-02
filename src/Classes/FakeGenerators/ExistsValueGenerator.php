<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators;

use ProgTime\RequestTestData\Classes\RequestCheckData;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExistsValueGenerator {

    /**
     * @param string $typeData
     * @param string|null $keyField
     * @param array $ruleParams
     * @param int $min
     * @param int $max
     * @return mixed|void|null
     */
    public function generate(string $typeData, ?string $keyField, array $ruleParams, int $min, int $max)
    {
        try {
            $uniqueParams = explode(',', $ruleParams['unique']);
            $tableName = $uniqueParams[0] ?? null;

            if (!empty($tableName)) {
                $uniqueColumn = $uniqueParams[1] ?? null;
                if (mb_strtolower($uniqueParams[1]) == 'null' && $keyField !== null) {
                    $uniqueColumn = $keyField;
                }

                if (!Schema::hasColumn($tableName, $uniqueColumn)) {
                    throw new \Exception('Field ' . $uniqueColumn . ' does not exist');
                }

                $exceptColumn = $uniqueParams[3] ?? null;

                $typeExceptColumnDB = Schema::getColumnType($tableName, $exceptColumn);
                $typeExceptColumnDB = preg_replace('/\d+/', '', $typeExceptColumnDB);

                $exceptValue = $this->conversionDataTypes($typeExceptColumnDB, $uniqueParams[2]) ?? null;

                $attempt = 0;
                $maxAttempts = 10;
                do {
                    $value = (new RequestCheckData())->getValueForField($ruleParams, $min, $max);

                    $attempt++;
                    if ($attempt >= $maxAttempts) {
                        throw new \Exception("Error!");
                    }
                } while ($this->checkExistsValue($tableName, $uniqueColumn, $exceptColumn, $exceptValue, $value));

            }

            return $value ?? null;
        } catch (\Exception $exception) {
            echo $exception->getMessage();
            die();
        }
    }

    private function checkExistsValue(string $tableName, string $uniqueColumn, ?string $exceptColumn, ?string $exceptValue, $value): bool
    {
        $query = DB::table($tableName)->where($uniqueColumn, $value);
        if (!empty($exceptColumn) && !empty($exceptValue)) {
            $query = $query->whereNot($exceptColumn, $exceptValue);
        }
        return $query->exists();
    }

}
