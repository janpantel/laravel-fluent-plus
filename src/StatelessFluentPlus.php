<?php

namespace JanPantel\LaravelFluentPlus;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;
use JanPantel\LaravelFluentPlus\Transformers\ArrayTransformer;
use JanPantel\LaravelFluentPlus\Transformers\CollectionTransformer;
use JanPantel\LaravelFluentPlus\Transformers\ObjectTransformer;
use JanPantel\LaravelFluentPlus\Transformers\TransformerInterface;

/**
 * Class StatelessFluentPlus
 *
 * Implements the FluentPlus logic without keeping global properties.
 * While this is the less pleasant way of doing things it makes serialization cheaper.
 *
 * If you don't mind serialization size the `FluentPlus` class should be used for a better experience.
 *
 * @package JanPantel\LaravelFluentPlus
 */
class StatelessFluentPlus extends Fluent
{
    /**
     * FluentPlus constructor.
     *
     * @param array|object $attributes
     * @param array? $casts
     * @param TransformerInterface[]? $caster
     */
    public function __construct($attributes = [])
    {
        parent::__construct(
            $this->prepare(
                $attributes,
                $this->getCasts(),
                $this->getTransformers(),
                $this->isRecursive()
            )
        );
    }

    /**
     * Returns the array of cast definitions.
     *
     * @return array
     */
    protected function getCasts()
    {
        return [];
    }

    /**
     * Determines whether sub-assocs should be parsed into a FluentPlus instance.
     *
     * @return bool
     */
    protected function isRecursive()
    {
        return true;
    }

    /**
     * Returns the array of casters.
     *
     * @return TransformerInterface[]
     */
    protected function getTransformers()
    {
        return [
            new ObjectTransformer,
            new ArrayTransformer,
            new CollectionTransformer,
        ];
    }

    /**
     * Prepares the attributes before they are handed to the base Fluent class.
     *
     * @param array|object $attributes
     * @param array $casts
     * @param TransformerInterface[] $transformers
     * @param boolean $isRecursive
     * @return array
     */
    private static function prepare($attributes, $casts, $transformers, $isRecursive)
    {
        foreach ($attributes as $key => $value) {
            $castDefinition = Arr::get($casts, $key);

            $transformer = null;
            if (! is_null($castDefinition)) {
                $transformer = self::findTransformer($castDefinition, $transformers, $value);
            }

            if (is_null($transformer)) {
                $transformer = function ($castDefinition, $value) use ($isRecursive) {
                    if ($isRecursive && is_array($value) && Arr::isAssoc($value)) {
                        return new FluentPlus($value);
                    }
                    return $value;
                };
            }

            if ($transformer instanceof TransformerInterface) {
                $transformer = function ($castDefinition, $value) use ($transformer) {
                    return $transformer->transform($castDefinition, $value);
                };
            }

            $attributes[$key] = $transformer($castDefinition, $value);
        }

        return $attributes;
    }

    /**
     * Returns the transformer to be used for the given cast definition.
     *
     * @param mixed $castDefinition
     * @param TransformerInterface[] $casters
     * @param mixed $value
     * @return TransformerInterface?|\Callable
     */
    private static function findTransformer($castDefinition, $casters, $value)
    {
        if ($castDefinition instanceof \Closure || $castDefinition instanceof TransformerInterface)
        {
            return $castDefinition;
        }

        return Arr::first($casters, function ($caster) use ($castDefinition, $value) {
            /** @var TransformerInterface $caster */
            return $caster->handles($castDefinition, $value);
        });
    }
}