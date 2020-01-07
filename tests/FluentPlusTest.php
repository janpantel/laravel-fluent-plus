<?php

namespace Tests\JanPantel\LaravelFluentPlus;

use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use JanPantel\LaravelFluentPlus\FluentPlus;
use PHPUnit\Framework\TestCase;

class FluentPlusTest extends TestCase
{
    public function testScalarType()
    {
        $prop = ClassCast::PROP . '_scalar';

        $instance = new ClassCast([$prop => 'foo']);

        $this->assertEquals('foo', $instance->$prop, 'Scalar type did not stay untouched');
    }

    public function testClassCast()
    {
        $prop = ClassCast::PROP;

        $instance = new ClassCast([$prop => ['bar']]);

        $this->assertTrue($instance->$prop instanceof SimpleClass);
    }

    public function testArrayCast()
    {
        $prop = ArrayCast::PROP;

        $instance = new ArrayCast([$prop => [[], []]]);

        $this->assertIsArray($instance->$prop);
        $this->assertTrue($instance->$prop[0] instanceof SimpleClass);
        $this->assertTrue($instance->$prop[1] instanceof SimpleClass);
    }

    public function testCollectionCast()
    {
        $prop = CollectionCast::PROP;

        $instance = new CollectionCast([$prop => [[], []]]);
        $this->assertTrue($instance->$prop instanceof Collection);
        $this->assertTrue($instance->$prop->get(0) instanceof SimpleClass);
        $this->assertTrue($instance->$prop->get(1) instanceof SimpleClass);
    }
}

class SimpleClass extends Fluent
{
}

class ClassCast extends FluentPlus
{
    const PROP = 'foo';

    protected function getCasts()
    {
        return [self::PROP => SimpleClass::class];
    }
}

class ArrayCast extends FluentPlus
{
    const PROP = 'bar';

    protected function getCasts()
    {
        return [
            self::PROP => [SimpleClass::class]
        ];
    }
}

class CollectionCast extends FluentPlus
{
    const PROP = 'baz';

    protected function getCasts()
    {
        return [
            self::PROP => [Collection::class, SimpleClass::class]
        ];
    }
}