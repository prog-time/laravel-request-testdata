<?php

namespace ProgTime\RequestTestData\Classes;

use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\CsvFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\GifFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\HtmlFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\JpgFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\JsonFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\LogFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\PdfFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\PngFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\SqlFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\SvgFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\TxtFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\XmlFileGenerator;
use ProgTime\RequestTestData\Classes\FakeGenerators\FileGenerators\YmlFileGenerator;

class FakerDataTypes {
    const TYPE_TEXT = [
        'string',
        'text',
        'email',
        'url',
        'uuid',
        'phone',
        'address',
        'ip',
        'ipv4',
        'ipv6',
        'timezone',
        'mac_address',
    ];

    const TYPE_NUMERIC = [
        'integer',
        'numeric',
        'boolean',
    ];

    const TYPE_DATE = [
        'date',
        'datetime',
        'time',
    ];

    const TYPE_FILE = [
        'image',
        'file',
    ];

    const TYPE_FILE_FORMAT = [
        'txt' => TxtFileGenerator::class,
        'csv' => CsvFileGenerator::class,
        'xml' => XmlFileGenerator::class,
        'pdf' => PdfFileGenerator::class,
        'jpg' => JpgFileGenerator::class,
        'jpeg' => JpgFileGenerator::class,
        'png' => PngFileGenerator::class,
        'gif' => GifFileGenerator::class,
        'json' => JsonFileGenerator::class,
        'html' => HtmlFileGenerator::class,
        'log' => LogFileGenerator::class,
        'sql' => SqlFileGenerator::class,
        'yml' => YmlFileGenerator::class,
        'svg' => SvgFileGenerator::class,
    ];

    const TYPE_RESOURCE = [
        'array',
        'json',
    ];

}
