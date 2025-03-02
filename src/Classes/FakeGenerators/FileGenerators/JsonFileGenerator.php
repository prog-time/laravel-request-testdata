<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use Faker\Factory as Faker;
use Illuminate\Http\File;

class JsonFileGenerator extends FileGeneratorAbstract
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
    protected function getFakeData(int $size): string
    {
        $dataFile = [];
        for ($i = 0; $i < ceil($size / 100); $i++) {
            $dataFile[] = ['id' => $i, 'name' => $this->faker->name(), 'email' => $this->faker->email()];
        }
        return json_encode($dataFile, JSON_PRETTY_PRINT);
    }
}
