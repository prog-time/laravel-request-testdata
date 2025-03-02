<?php

namespace ProgTime\RequestTestData\Classes\FakeGenerators;

interface DataGeneratorInterface
{
    /**
     * @param string $typeData
     * @param int $min
     * @param int $max
     * @return mixed
     */
    public function generate(string $typeData, int $min = 1, int $max = 0): mixed;
}
