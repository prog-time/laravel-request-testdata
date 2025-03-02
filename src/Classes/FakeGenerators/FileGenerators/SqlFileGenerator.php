<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use Faker\Factory as Faker;
use Illuminate\Http\File;

class SqlFileGenerator extends FileGeneratorAbstract
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
        $dataFile = "";
        while (strlen($dataFile) < $size) {
            $sql = "INSERT INTO `users` (`name`, `email`) VALUES ('" . $this->faker->name() . "', '" . $this->faker->email() . "');\n";

            $newCount = strlen($dataFile) + strlen($sql);
            if ($newCount > $size) {
                break;
            }

            $dataFile .= $sql;
        }

        return substr($dataFile, 0, $size);
    }
}
