<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use Faker\Factory as Faker;
use Illuminate\Http\File;

class SvgFileGenerator extends FileGeneratorAbstract
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
        $dataFile = "<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100'>";
        while (strlen($dataFile) < $size) {
            $circle = '<circle cx="50" cy="50" r="40" stroke="black" stroke-width="3" fill="red" />';

            $newCount = strlen($dataFile) + strlen($circle);
            if ($newCount > $size) {
                break;
            }

            $dataFile .= $circle;
        }

        $dataFile .= "</svg>";

        return substr($dataFile, 0, $size);
    }
}
