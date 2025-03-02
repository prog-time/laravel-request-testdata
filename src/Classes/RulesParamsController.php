<?php

namespace ProgTime\RequestTestData\Classes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Dimensions;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\Rules\NotIn;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\Validation\Rules\Unique;

class RulesParamsController
{

    /**
     * @param FormRequest $classRequest
     * @return array
     */
    public function getRequestRulesParams(FormRequest $classRequest): array
    {
        try {
            $rules = $classRequest->rules();
            $resultRulesParams = [];

            foreach ($rules as $keyRow => $param) {
                $rulesList = $param;
                if (is_string($rulesList)) {
                    $rulesList = explode('|', $rulesList);
                }

                if (is_array($rulesList)) {
                    $fieldRules = [];
                    foreach ($rulesList as $rule) {
                        if ($rule instanceof Unique ||
                            $rule instanceof Exists ||
                            $rule instanceof NotIn ||
                            $rule instanceof In ||
                            $rule instanceof RequiredIf) {
                            if (!empty($rule->__toString())) {
                                $rule = $rule->__toString();
                            }
                        }

                        if (is_string($rule)) {
                            $fieldRules = array_merge(($fieldRules ?? []), $this->checkRuleParams($rule));
                        }
                    }

                    if (!empty($fieldRules)) {
                        $forbiddenKeys = [
                            'prohibited',
                            'prohibited_unless',
                            'prohibits',
                        ];
                        if (!empty(array_intersect_key($fieldRules, array_flip($forbiddenKeys)))) {
                            continue;
                        }

                        $resultRulesParams[$keyRow] = $fieldRules;
                    }
                }
            }

            return $resultRulesParams ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @param string $rule
     * @return array
     */
    private function checkRuleParams(string $rule): array
    {
        $resultRules = [];
        $ruleParams = explode(':', $rule, 2);

        if (count($ruleParams) === 2) {
            switch ($ruleParams[0]) {
                case 'alpha':
                    $resultRules['regex'] = "/^[a-zA-Z]+$/";
                    break;

                case 'alpha_dash':
                    $resultRules['regex'] = "/^[a-zA-Z0-9_-]+$/";
                    break;

                case 'alpha_num':
                    $resultRules['regex'] = "/^[a-zA-Z0-9]+$/";
                    break;

                case 'size':
                    $resultRules['min'] = $ruleParams[1];
                    $resultRules['max'] = $ruleParams[1];
                    break;

                case 'mimes':
                    $resultRules['file'] = true;
                    $resultRules['mimes'] = explode(',', $ruleParams[1]);
                    break;

                case 'dimensions':
                    $resultRules['file'] = true;
                    $dimensions = explode(',', $ruleParams[1]);

                    $list = [];
                    foreach ($dimensions as $dimension) {
                        if (preg_match('/^\w+=\d+$/', $dimension)) {
                            $param = explode('=', $dimension);
                            $list[$param[0]] = $param[1];
                        }
                    }
                    $resultRules['dimensions'] = $list;
                    break;

                case 'digits':
                    $countNumeric = (int) $ruleParams[1] - 1;
                    $resultRules['digits'] = $countNumeric;
                    $resultRules['min'] = (int) ('1' . str_repeat('0', $countNumeric));
                    $resultRules['max'] = (int) ('9' . str_repeat('9', $countNumeric));
                    break;

                case 'digits_between':
                    $params = explode(',', $ruleParams[1]);
                    $resultRules['integer'] = true;
                    $resultRules['min'] = (int) $params[0];
                    $resultRules['max'] = (int) $params[1];
                    break;

                case 'date_format':
                    $resultRules['date'] = true;
                    break;

                case 'in':
                case 'not_in':
                    $params = explode(',', $ruleParams[1]);
                    $resultRules[$ruleParams[0]] = array_map(function ($item) {
                        return trim($item, '"');
                    }, $params);
                    break;

                case 'gt':
                    $resultRules['min'] = (int)$ruleParams[1];
                    break;

                case 'gte':
                    $resultRules['min'] = (int)$ruleParams[1] + 1;
                    break;

                case 'lt':
                    $resultRules['max'] = (int)$ruleParams[1] - 1;
                    break;

                case 'lte':
                    $resultRules['max'] = (int)$ruleParams[1];
                    break;

                default:
                    $resultRules[$ruleParams[0]] = $ruleParams[1];
            }

        } else {
            $resultRules[$rule] = true;
        }

        return $resultRules;
    }

}
