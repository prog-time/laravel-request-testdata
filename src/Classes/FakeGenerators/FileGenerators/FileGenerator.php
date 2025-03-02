<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators;

use ProgTime\RequestTestData\Classes\FakerDataTypes;
use Illuminate\Http\File;

class FileGenerator
{
    protected $generators = [];

    public function __construct()
    {
        $this->generators = FakerDataTypes::TYPE_FILE_FORMAT;
    }

    /**
     * @param string $fileType
     * @param int $size
     * @param array $ruleParams
     * @return File|null
     * @throws \Exception
     */
    public function generate(string $fileType, int $size, array $ruleParams): ?File
    {
        if (!isset($this->generators[$fileType])) {
            throw new \Exception("Unsupported file type: $fileType");
        }

        $size = ($size > 0) ? $size : 1000;

        $generator = app($this->generators[$fileType]);
        return $generator->generate($fileType, $size, $ruleParams);
    }
}
