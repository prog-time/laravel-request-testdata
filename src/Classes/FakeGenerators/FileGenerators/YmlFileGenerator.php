<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use Faker\Factory as Faker;
use Illuminate\Http\File;

class YmlFileGenerator extends FileGeneratorAbstract
{
    private \Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * @param string $format
     * @param int $size
     * @param array|null $ruleParams
     * @return File|null
     */
    public function generate(string $format, int $size, ?array $ruleParams): ?File
    {
        try {
            return $this->saveFile($format, $this->getFakeData($size));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param int $size
     * @return string
     */
    protected function getFakeData(int $size)
    {
        $dataFile = "version: '3.8' \n";
        while (strlen($dataFile) < $size) {

            $dataFile .= $this->faker->word() . ": \n";
            for ($i = 0; $i < 3; $i++) {
                $key = $this->faker->word();
                $value = $this->faker->word();

                $indent = str_repeat('  ', 2);
                $dataFile .= "{$indent}{$key}: $value\n";
            }
        }
        return substr($dataFile, 0, $size);
    }
}
