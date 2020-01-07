<?php

namespace Tests\JanPantel\LaravelFluentPlus;

use JanPantel\LaravelFluentPlus\FluentPlus;
use JanPantel\LaravelFluentPlus\Transformers\SinglePropertyTransformer;
use PHPUnit\Framework\TestCase;

class SinglePropertyCustomTransformerTest extends TestCase
{
    public function testSinglePropertyIsTranformedUsingTransformerFromCastsArray()
    {
        $prop = SinglePropertyCastFluent::PROP;

        $instance = new SinglePropertyCastFluent([$prop => 'something']);

        $this->assertEquals(DummySinglePropertyTransformer::RETURN_VALUE, $instance->$prop);
    }
}

class SinglePropertyCastFluent extends FluentPlus
{
    const PROP = 'foo';

    protected function getCasts()
    {
        return [
            self::PROP => new DummySinglePropertyTransformer
        ];
    }
}

class DummySinglePropertyTransformer extends SinglePropertyTransformer
{
    const RETURN_VALUE = 'thisiscustom!';

    /**
     * @inheritDoc
     */
    public function transform($castDefinition, $value)
    {
        return self::RETURN_VALUE;
    }
}