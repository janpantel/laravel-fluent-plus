<?php

namespace Tests\JanPantel\LaravelFluentPlus;


use JanPantel\LaravelFluentPlus\FluentPlus;
use JanPantel\LaravelFluentPlus\NonRecursiveFluentPlus;
use PHPUnit\Framework\TestCase;

class RecursiveFluentTest extends TestCase
{
    public function testRecursiveCast()
    {
        $instance = new RecursiveCast(['foo' => ['bar' => 'baz']]);

        $this->assertEquals('baz', $instance->foo->bar);
    }

    public function testOnlyAssocsCastedRecursively()
    {
        $instance = new RecursiveCast(['foo' => ['bar', 'baz']]);

        $this->assertIsArray($instance->foo);
    }

    public function testMultiDimensionRecursive()
    {
        $instance = new RecursiveCast(['foo' => ['bar' => ['baz' => ['hello' => 'world']]]]);

        $this->assertEquals('world', $instance->foo->bar->baz->hello);
    }

    public function testInlineNonRecursive()
    {
        $instance = new NonRecursiveFluentPlus(['foo' => ['bar' => 'baz']]);

        $this->assertIsArray($instance->foo);
    }
}

class RecursiveCast extends FluentPlus
{
    protected $recursive = true;
}