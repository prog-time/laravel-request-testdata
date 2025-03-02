<?php

namespace ProgTime\RequestTestData;

use ProgTime\RequestTestData\Classes\RequestTestingData;
use ProgTime\RequestTestData\Classes\RulesParamsController;
use Illuminate\Foundation\Http\FormRequest;

class RequestDataGenerator
{
    /**
     * @param FormRequest $requestClass
     * @return array
     */
    public static function generate(FormRequest $requestClass): array
    {
        try {
            $requestArguments = (new RulesParamsController())->getRequestRulesParams($requestClass);
            return (new RequestTestingData())->getCurrentRequestTestingData($requestArguments, $requestClass);
        } catch (\Exception $e) {
            return [];
        }
    }
}
