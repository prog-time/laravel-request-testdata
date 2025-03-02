<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

abstract class FileGeneratorAbstract
{
    protected string $directory = 'faker_file';
    protected string $fileName = 'fake_file';

    private \Faker\Generator $faker;

    /**
     * @param string $format
     * @return string
     */
    protected function getFilePath(string $format): string
    {
        return $this->directory . '/' . $this->fileName . '.' . $format;
    }

    /**
     * @param string $format
     * @param mixed $fileData
     * @return File|null
     */
    protected function saveFile(string $format, mixed $fileData): ?File
    {
        try {
            $filePath = $this->getFilePath($format);
            $result = Storage::put($filePath, $fileData);

            if (empty($result)) {
                throw new \Exception("Failed to write test file");
            }

            return new File(Storage::path($filePath));
        } catch (\Exception $exception) {
            return null;
        }
    }

    public abstract function generate(string $format, int $size, ?array $ruleParams): ?File;

    protected abstract function getFakeData(int $size);
}
