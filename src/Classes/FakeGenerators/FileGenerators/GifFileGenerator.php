<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use Faker\Factory as Faker;
use Illuminate\Http\File;

class GifFileGenerator extends ImageGeneratorAbstract
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
            $imageData = $this->getFakeImageData($format, $size, $ruleParams);
            return $this->saveFile($format, $imageData);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getFakeData($size)
    {
    }

}
