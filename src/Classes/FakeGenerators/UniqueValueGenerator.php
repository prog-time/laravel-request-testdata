<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators;

use ProgTime\RequestTestData\Classes\RequestCheckData;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UniqueValueGenerator {

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

    private function generateDataTypes(string $typeFieldDB)
    {
        $faker = Faker::create();
        $fakerMapping = [
            'int' => $faker->numberBetween(1, 10000),
            'bigint' => $faker->numberBetween(100000, 999999),
            'tinyint' => $faker->boolean,
            'smallint' => $faker->numberBetween(1, 100),
            'mediumint' => $faker->numberBetween(100, 10000),
            'decimal' => $faker->randomFloat(2, 1, 1000),
            'float' => $faker->randomFloat(2, 1, 1000),
            'double' => $faker->randomFloat(6, 1, 100000),

            'char' => $faker->randomLetter,
            'varchar' => $faker->word,
            'text' => $faker->sentence,
            'tinytext' => $faker->sentence(3),
            'mediumtext' => $faker->paragraph,
            'longtext' => $faker->text(1000),

            'json' => json_encode(['key' => $faker->word, 'value' => $faker->randomNumber()]),

            'date' => $faker->date(),
            'datetime' => $faker->dateTime(),
            'timestamp' => $faker->dateTime(),
            'time' => $faker->time(),
            'year' => $faker->year(),

            'boolean' => $faker->boolean,

            'uuid' => $faker->uuid,
            'ip' => $faker->ipv4,
            'jsonb' => json_encode([$faker->word => $faker->word]),
        ];

        return !empty($fakerMapping[$typeFieldDB]) ? $fakerMapping[$typeFieldDB] : null;
    }

    private function conversionDataTypes(string $typeFieldDB, mixed $value): mixed
    {
        $typeConversions = [
            'int' => (int) $value, // Преобразует в целое число
            'bigint' => (int) $value, // То же, но для больших чисел
            'tinyint' => (bool) $value, // 0 или 1 (если строка не пустая — true)
            'smallint' => (int) $value,
            'mediumint' => (int) $value,
            'decimal' => (float) $value, // Преобразует в число с плавающей точкой
            'float' => (float) $value,
            'double' => (double) $value,

            'char' => substr($value, 0, 1), // Берёт только первый символ
            'varchar' => (string) $value, // Преобразует в строку
            'text' => (string) $value, // То же самое
            'tinytext' => (string) $value,
            'mediumtext' => (string) $value,
            'longtext' => (string) $value,

            'json' => json_encode([$value]), // Оборачивает в JSON-массив

            'date' => date('Y-m-d', strtotime($value)), // Преобразует в дату
            'datetime' => date('Y-m-d H:i:s', strtotime($value)), // В дату и время
            'timestamp' => strtotime($value), // Unix-время
            'time' => date('H:i:s', strtotime($value)),
            'year' => date('Y', strtotime($value)),

            'enum' => $value, // Оставляем как есть (обычно ENUM ограничен)

            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN), // Преобразует в true/false

            'uuid' => $value, // UUID остаётся строкой
            'ip' => filter_var($value, FILTER_VALIDATE_IP) ? $value : '127.0.0.1', // Проверяем IP, иначе дефолтный
            'jsonb' => json_encode([$value]), // То же, что и json
        ];

        return !empty($typeConversions[$typeFieldDB]) ? $typeConversions[$typeFieldDB] : null;
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
