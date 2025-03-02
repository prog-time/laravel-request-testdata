<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use Dompdf\Dompdf;
use Dompdf\Options;
use Faker\Factory as Faker;
use Illuminate\Http\File;

class PdfFileGenerator extends FileGeneratorAbstract
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
            $options = new Options();
            $options->set('defaultFont', 'Helvetica');

            $dompdf = new Dompdf($options);
            $html = "<h1>Faker file!</h1><p>{$this->getFakeData($size)}</p>";
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $fileData = $dompdf->output();

            return $this->saveFile($format, $fileData);
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
        return str_repeat($this->faker->text(200), ceil($size / 200));
    }
}
