<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use Faker\Factory as Faker;
use Illuminate\Http\File;

class HtmlFileGenerator extends FileGeneratorAbstract
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
        $html = '';
        while (strlen($html) < $size) {
            $tag = $this->faker->randomElement(['p', 'h1', 'h2', 'h3', 'ul', 'li', 'span', 'div', 'a']);
            $content = $this->faker->sentence();

            if ($tag === 'ul') {
                $html .= "<ul>";
                for ($i = 0; $i < 3; $i++) {
                    $html .= "<li>" . $this->faker->word() . "</li>";
                }
                $html .= "</ul>";
            } else {
                $html .= "<$tag>$content</$tag>";
            }
        }

        return substr($html, 0, $size);
    }
}
