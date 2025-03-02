<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use Faker\Factory as Faker;
use Illuminate\Http\File;

class XmlFileGenerator extends FileGeneratorAbstract
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
        $xml = new \SimpleXMLElement('<root/>');
        for ($i = 0; $i < ceil($size / 100); $i++) {
            $item = $xml->addChild('user');
            $item->addChild('id', $i);
            $item->addChild('name', $this->faker->name());
            $item->addChild('email', $this->faker->email());
        }
        return $xml->asXML();
    }
}
