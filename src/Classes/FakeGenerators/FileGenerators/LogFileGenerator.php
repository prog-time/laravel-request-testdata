<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use Faker\Factory as Faker;
use Illuminate\Http\File;

class LogFileGenerator extends FileGeneratorAbstract
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
        $logStr = "";
        while (strlen($logStr) < $size) {
            $logStr .= "[". date('Y-m-d H:m:s') ."] INFO: ";
            $logStr .= $this->faker->text(200);
            $logStr .= "\n";
        }
        return substr($logStr, 0, $size);
    }
}
