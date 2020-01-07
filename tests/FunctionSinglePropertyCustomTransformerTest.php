<?php

namespace Tests\JanPantel\LaravelFluentPlus;

use JanPantel\LaravelFluentPlus\FluentPlus;
use PHPUnit\Framework\TestCase;

class FunctionSinglePropertyCustomTransformerTest extends TestCase
{
    public function testSinglePropertyIsTranformedUsingAFunctionTransformerFromCastsArray()
    {
        $prop = FunctionSinglePropertyCastFluent::PROP;

        $instance = new FunctionSinglePropertyCastFluent([$prop => 'something']);

        $this->assertEquals('something_transformed', $instance->$prop);
    }
}

class FunctionSinglePropertyCastFluent extends FluentPlus
{
    const PROP = 'functionprop';

    protected function getCasts()
    {
        return [
            self::PROP => function ($castDefinition, $value) {
                return $value . '_transformed';
            }
        ];
    }
}